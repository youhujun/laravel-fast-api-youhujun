<?php

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Facades\Common\V1\Es\CommonEsFacade;

if (!function_exists('make_system_config')) {
    /**
     * 初始化系统配置缓存
     *
     * 从Redis获取系统配置列表，若不存在则从Es加载并缓存到Redis。
     * 同时检查系统配置是否已设置，未设置则将各项配置按类型存入缓存。
     *
     * 流程：
     * 1. 尝试从Redis获取已缓存的系统配置列表
     * 2. 若不存在则从数据库查询并缓存到Redis
     * 3. 检查系统配置是否已初始化
     * 4. 未初始化则遍历配置项，按item_type将对应值存入缓存
     *
     * 缓存结构：
     * - Redis哈希'system:config'存储:
     *   - 'listSystemConfig': 序列化的系统配置列表
     *   - 'isSetSystemConfig': 配置是否已初始化的标记(0/1)
     * - Cache中按item_label存储各项配置值
     */
    function make_system_config()
    {
        //是否缓存系统配置
        $is_cache_system_config = config('common.is_cache_system_config');

        if ($is_cache_system_config) {
            $systemConfigRedis = Redis::hget('system:config', 'listSystemConfig');
            if ($systemConfigRedis) {
                $systemConfigCollection = unserialize($systemConfigRedis);
            } else {
                $systemConfigCollection = CommonEsFacade::getEsSystemConfig();

                //p($systemConfigList);die;

                Redis::hset('system:config', 'listSystemConfig', serialize($systemConfigCollection));
            }

            if ($systemConfigCollection->count()) {
                foreach ($systemConfigCollection as $key => $item) {
                    $value = [10 => $item->item_label,20 => $item->item_value,30 => $item->item_price,40 => $item->item_path,50 => $item->item_path];

                    if ($value[$item->item_type]) {
                        $result = Cache::put($item->item_label, $value[$item->item_type]);
                    }
                }
            }
        }
    }
}


if (!function_exists('clean_system_config')) {
    /**
     * 清理系统配置缓存
     *
     * 将Redis中的系统配置标记重置为未设置状态，并清空配置列表
     *
     * @return void
     */
    function clean_system_config()
    {
        Redis::hset('system:config', 'listSystemConfig', null);
    }
}



/**
 * 处理时间结束
 */

if (!function_exists('em')) {
    /**
     * 处理捕获的异常并根据需要记录日志或发送通知
     *
     * @param \Throwable $e 捕获的异常对象
     * @param boolean $log 是否将异常信息写入日志，默认为 false
     * @param boolean $notification 是否发送异常通知，默认为 false
     * @return string 返回格式化的异常信息
     */

    function em(\Throwable $e, $log = false, $notification = false)
    {
        $time = date('Y-m-d H:i:s', time());
        $msg = "时间:{$time}";
        $msg .= "\r\n异常消息：".($e->message ? $e->message : $e->getMessage());
        $msg .= "\r\n错误码:".$e->getCode();
        $msg .= "\r\n文件：".$e->getFile();
        $msg .= "\r\n行号：".$e->getLine();
        $msg .= "\r\n参数:".json_encode(['params' => request()->all()]);

        if ($log) {
            \file_put_contents(\storage_path()."/logs/exception.log", "{$msg}\r\n", \FILE_APPEND);
        }

        if ($notification) {
            if (env('YouHuJun_Publish')) {
                Notification::route('mail', config('mail.exception.email'))
                ->notify(new \App\Notifications\LaravelFastApi\V1\SendExceptionMessageNoification($msg));
            }
        }

        return $msg;
    }
}
