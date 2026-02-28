<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-02-13 16:10:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-28 11:50:48
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\App\Providers\LaravelFastApiServiceProvider.php
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\LaravelFastApi\V1\System\SystemConfig;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use Illuminate\Support\Facades\Config;

class LaravelFastApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfig();
        //调用合并配置文件

        if (config('youhujun.publish')) {
            $this->addAliasMiddleware('admin.login', \App\Http\Middleware\LaravelFastApi\V1\AdminTokenMiddleware::class);

            $this->addAliasMiddleware('phone.login', \App\Http\Middleware\LaravelFastApi\V1\PhoneTokenMiddleware::class);

            $this->addAliasMiddleware('youhubase.sign', \App\Http\Middleware\LaravelFastApi\V1\SignAuthMiddleWare::class);

            $this->addAliasMiddleware('youhubase.auth', \App\Http\Middleware\LaravelFastApi\V1\AuthTokenMiddleWare::class);
        } else {
        }
    }

    public function boot()
    {
        Schema::defaultStringLength(255);

        //记录sql日志
        $this->logSql();

        // 全局初始化ShardFacade配置（只执行一次，所有地方复用）
        ShardFacade::setMultiConfig('youhujun', [
            'db_count' => Config::get('youhujun.shard.db_count', 1),
            'table_count' => Config::get('youhujun.shard.table_count', 1),
            'db_prefix' => Config::get('youhujun.shard.db_prefix', 'ds_'),
            'default_db' => Config::get('youhujun.shard.default_db', 'ds_0'),
        ]);

        if (config('youhujun.publish')) {
            //执行运行数据库迁移文件
            $this->loadRun();
        }

        //运行时动态修改配置文件
        if (config('youhujun.runing')) {
            //启动自定义命令
            $this->registerCommand();

            $this->makeConfig();
        }

        //添加到中间件分组api中 !!!!注意这里只能在这里生效,注册的时候虽然能添加上,但是不生效
        $this->addMiddlewareGroup('api', \App\Http\Middleware\LaravelFastApi\V1\TimeVerifyMiddleware::class);
    }

    /**
     * 记录sql日志
     */
    protected function logSql()
    {
        //监听sql
        DB::listen(function (QueryExecuted $query) {
            $sql = $query->sql;
            $bindings = $query->bindings;

            foreach ($bindings as $binding) {
                $processedBinding = match (true) {
                    // 字符串用双引号包裹，避免单引号转义
                    is_string($binding) => "\"" . $binding . "\"",
                    is_bool($binding) => $binding ? '1' : '0',
                    is_null($binding) => 'NULL',
                    // 时间日期也用双引号包裹
                    $binding instanceof \DateTimeInterface => "\"" . $binding->format('Y-m-d H:i:s') . "\"",
                    is_array($binding) => implode(',', array_map(function ($item) {
                        // 数组中的字符串同样用双引号
                        return is_string($item) ? "\"" . $item . "\"" : $item;
                    }, $binding)),
                    default => $binding
                };

                // 替换 SQL 中的 ? 占位符
                $sql = preg_replace('/\?/', $processedBinding, $sql, 1);
            }

            $data = [
                'sql' => $sql,
                'param' => $bindings,
                'time' => $query->time
            ];

            plog(['sqldata' => $data], 'sql', 'sqllog');
        });
    }

    /**
     * 运行时动态修改配置文件
     *
     * @return void
     */
    protected function makeConfig()
    {
        $systemConfigRedis = Redis::hget('system:config', 'listSystemConfig');

        if ($systemConfigRedis) {
            $systemConfigList = unserialize($systemConfigRedis);
        } else {
            $systemConfigList = SystemConfig::all();

            //p($systemConfigList);die;

            Redis::hset('system:config', 'listSystemConfig', serialize($systemConfigList));
        }

        if ($systemConfigList->count()) {
            $isSetSystemConfig = Redis::hget('system:config', 'isSetSystemConfig');

            if (!$isSetSystemConfig) {
                foreach ($systemConfigList as $key => $item) {
                    $value = [10 => $item->item_label,20 => $item->item_value,30 => $item->item_price,40 => $item->item_path,50 => $item->item_path];

                    if ($value[$item->item_type]) {
                        $result = Cache::put($item->item_label, $value[$item->item_type]);
                    }
                }

                Redis::hset('system:config', 'isSetSystemConfig', 1);
            }
        }
    }


    /**
     * 自定义配置文件
     *
     * @return void
     */
    protected function mergeConfig()
    {
        if (config('help.youhujun_custom')) {
            //自定义配置文件
            $this->mergeConfigFrom(
                config_path('custom/custom.php'),
                'custom',
            );

            //youhujun配置文件
            $this->mergeConfigFrom(
                config_path('custom/laravel-fast-api/youhujun.php'),
                'youhujun'
            );
        }

        if (config('youhujun.publish')) {
            $this->mergeCommonConfig();
            $this->mergeAdminConfig();
            $this->mergePhoneConfig();
        }
    }

    /**
     * 自定义通用配置文件
     */
    protected function mergeCommonConfig()
    {
        //common配置文件
        $this->mergeConfigFrom(
            config_path('custom/common/common.php'),
            'common'
        );

        //错误码
        $this->mergeConfigFrom(
            config_path('custom/common/code/common_code.php'),
            'common_code'
        );

        //事件码
        $this->mergeConfigFrom(
            config_path('custom/common/event/common_event.php'),
            'common_event'
        );

        //后台错误码
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/public/code/common_code.php'),
            'common_code'
        );

        //短信错误码
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/public/code/common_sms_code.php'),
            'common_code'
        );

        //微信公众号
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/public/code/common_wechat_code.php'),
            'common_code'
        );

        //地图
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/public/code/common_map_code.php'),
            'common_code'
        );

        //自定义验证规则错误码
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/public/rule/rule_code.php'),
            'rule_code'
        );

        //api的url
        $this->mergeCommonApiConfig();
    }

    /**
     * 自定义微服务apiurl
     */
    protected function mergeCommonApiConfig()
    {
        $this->mergeConfigFrom(
            config_path('custom/common/api/youhubase.php'),
            'youhubase_api_url'
        );


        $this->mergeConfigFrom(
            config_path('custom/common/api/shardmap.php'),
            'shardmap_api_url'
        );


        $this->mergeConfigFrom(
            config_path('custom/common/api/youhu.php'),
            'youhu_api_url'
        );

        $this->mergeConfigFrom(
            config_path('custom/common/api/youhushop.php'),
            'youhushop_api_url'
        );
    }

    /**
     * 自定义后台配置文件
     */
    protected function mergeAdminConfig()
    {
        //后台错误码
        //整合
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_code.php'),
            'admin_code'
        );
        //系统配置
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_system_code.php'),
            'admin_code'
        );
        //文章
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_article_code.php'),
            'admin_code'
        );
        //文件
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_file_code.php'),
            'admin_code'
        );

        //日志
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_log_code.php'),
            'admin_code'
        );
        //登录
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_login_code.php'),
            'admin_code'
        );

        //其他
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_other_code.php'),
            'admin_code'
        );
        //图片空间
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_picture_code.php'),
            'admin_code'
        );
        //用户
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/code/admin_user_code.php'),
            'admin_code'
        );

        //后台事件码
        //全部
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_event.php'),
            'admin_event'
        );
        //文章
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_article_event.php'),
            'admin_event'
        );
        //文件
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_file_event.php'),
            'admin_event'
        );

        //图片空间
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_picture_event.php'),
            'admin_event'
        );
        //系统
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_system_event.php'),
            'admin_event'
        );
        //用户
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/admin/event/admin_user_event.php'),
            'admin_event'
        );
    }

    /**
     * 自定义手机配置文件
     */
    protected function mergePhoneConfig()
    {
        //手机错误码
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/code/phone_code.php'),
            'phone_code'
        );
        //登录注册
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/code/phone_login_code.php'),
            'phone_code'
        );
        //文件
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/code/phone_file_code.php'),
            'phone_code'
        );
        //支付
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/code/phone_pay_code.php'),
            'phone_code'
        );
        //手机用户
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/code/phone_user_code.php'),
            'phone_code'
        );

        //手机事件码
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/event/phone_event.php'),
            'phone_event'
        );
        //文件
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/event/phone_file_event.php'),
            'phone_event'
        );
        //用户
        $this->mergeConfigFrom(
            config_path('custom/laravel-fast-api/phone/event/phone_user_event.php'),
            'phone_event'
        );
    }



    /**
     * 自定义运行
     *
     * @return void
     */
    protected function loadRun()
    {
        //运行数据库迁移
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        //系统公共
        $this->loadRunCommon();
        //用户模块
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/User');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/User/Info');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/User/Log');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/User/Platform');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/User/Union');

        //用户管理--管理员管理
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Admin');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Admin/Log');

        //文章管理
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Article');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Article/Info');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Article/Union');

        //图片空间
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Picture');
        //任务
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Job');
        //支付
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Pay');
    }

    /**
     *自定义运行公共的
     */
    protected function loadRunCommon()
    {
        //鉴权模块
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Api');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/Api/Auth');

        //系统模块
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System');
        //系统配置
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Config');
        //系统--级别
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Level');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Level/Union');
        //系统--模块
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Module');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Module/Article');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Module/Goods');

        //系统-权限
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Permission');
        //系统-平台
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Platform');
        //系统-地区
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Region');
        //系统-角色
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Role');
        //系统-关联
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations/Create/System/Union');
    }

    /**
     * @param string $name
     * @param string $class
     * @return void
     */
    protected function addAliasMiddleware(string $name, string $class)
    {
        $router = $this->app['router'];
        //执行完相当于在app/Http/Kernel.php中注册了中间件
        $router = $router->aliasMiddleware($name, $class);
        return $router;
    }

    /**
     * @param string $group
     * @param string $class
     * @return void
     */
    protected function addMiddlewareGroup(string $group, string $class)
    {
        $router = $this->app['router'];

        $router = $router->pushMiddlewareToGroup($group, $class);

        //dd($result);
        //执行完相当于在app/Http/Kernel.php中注册了中间件
        return $router;
    }



    /**
     * @description: 注册自定义命令
     * @param {type}
     * @return:
     */
    protected function registerCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \YouHuJun\LaravelFastApi\App\Console\Commands\BuildFacadeCommand::class,
                \YouHuJun\LaravelFastApi\App\Console\Commands\BuildFacadeServiceCommand::class,
                \YouHuJun\LaravelFastApi\App\Console\Commands\CallFacadeServiceCommand::class,
                \YouHuJun\LaravelFastApi\App\Console\Commands\BuildContractCommand::class,
                \YouHuJun\LaravelFastApi\App\Console\Commands\BuildContractServiceCommand::class,
                \YouHuJun\LaravelFastApi\App\Console\Commands\BuildDTOCommand::class
            ]);
        }
    }
}
