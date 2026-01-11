<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-28 14:19:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:47:33
 * @FilePath: \app\Service\Facade\Pub\V1\Wechat\WechatLoginFacadeService.php
 */

namespace App\Service\Facade\Pub\V1\Wechat;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Common\CommonException;
use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use YouHuJun\Tool\App\Facade\V1\Wechat\MiniProgram\WechatMiniProgramFacade;

/**
 * @see \App\Facade\Pub\V1\Wechat\WechatLoginFacade
 */
class WechatLoginFacadeService
{
    public function test()
    {
        echo "WechatLoginFacadeService test";
    }

    /**
     * 微信小程序登录
     */
    public function getOpenIdByCodeWithMiniProgram($param)
    {
        if (!(isset($param['code']) || isset($param['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($param['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code,'appid' => $appid] = $param;

        $systemWechatConfigObject = null;

        $systemWechatConfigString =  Cache::get('systemWechatConfigObject');

        if ($systemWechatConfigString) {
            $systemWechatConfigObject = unserialize($systemWechatConfigString);
        }

        if (!$systemWechatConfigObject) {
            $systemWechatConfigCount = SystemWechatConfig::where('appid', $appid)->where('type', 10)->orWhere('type', 20)->count();

            if (!$systemWechatConfigCount) {
                throw new CommonException('SystemWechatConfigIsNullError');
            }

            $systemWechatConfigObject = SystemWechatConfig::where('appid', $appid)->where('type', 10)->orWhere('type', 20)->first();

            Cache::put('systemWechatConfigObject', serialize($systemWechatConfigObject));
        }

        $systemWechatConfigId = $systemWechatConfigObject->id;

        $secret = $systemWechatConfigObject->appsecret;

        // 登录参数
        $params = [
            'code' => $code,
            'appid' => $appid,
        ];

        // 配置
        $config = [
            'appsecret' => $secret,
        ];

        // 获取用户信息(返回数组)
        $result = WechatMiniProgramFacade::getOpenIdByCode($params, $config);


        //{"session_key":"CJsCJOkD3mFX8weh1ijyBQ==","openid":"ok-X068nPIY-emwpdlYAmoaef2h8","unionid":"o7hrD6aYhmgFFy_qb238EOfKCS8I"}

        $collection = collect(['response' => $result,'appid' => $appid,'systemWechatConfigId' => $systemWechatConfigId]);

        return $collection;
    }
}
