<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-09 17:26:11
 * @FilePath: \app\Service\Facade\Pub\V1\DouYin\DouYinFacadeService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Service\Facade\Pub\V1\DouYin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;
use App\Models\LaravelFastApi\V1\System\Platform\SystemDouyinConfig;
use YouHuJun\Tool\App\Facade\V1\DouYin\Login\DouYinLoginFacade;

/**
 * @see \App\Facade\Pub\V1\DouYin\DouYinFacade
 */
class DouYinFacadeService
{
    public function test()
    {
        echo "DouYinFacadeService test";
    }

    /**
    * 执行抖音小游戏登录
    *
    * @param  [array] $param
    */
    public function getOpenIdByCodeWithMiniGame($param)
    {
        if (!(isset($param['code']) || isset($param['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($param['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code,'anonymousCode' => $anonymousCode,'appid' => $appid] = $param;

        $systemDouyinConfigCount = SystemDouyinConfig::where('appid', $appid)->where('type', 20)->count();

        if (!$systemDouyinConfigCount) {
            throw new CommonException('SystemDouyinConfigIsNullError');
        }

        $systemDouyinConfigObject = SystemDouyinConfig::where('appid', $appid)->where('type', 20)->first();

        $systemDouyinConfigId = $systemDouyinConfigObject->id;

        $secret = $systemDouyinConfigObject->appsecret;

        $params = [
            'code' => $code,
            'appid' => $appid
        ];

        $config = [
            'appsecret' => $secret
        ];

        $result = DouYinLoginFacade::getOpenIdByCodeWithMiniGame($params, $config);

        $collection = collect(['result' => $result,'appid' => $appid,'systemDouyinConfigId' => $systemDouyinConfigId]);

        return $collection;
    }

    /**
     * 抖音小程序登录
     *
     * @param  [type] $param
     */
    public function getOpenIdByCodeWithMiniProgram($param)
    {
        if (!(isset($param['code']) || isset($param['anonymousCode']))) {
            throw new CommonException('ParamsIsNullError');
        }

        if (!isset($param['appid'])) {
            throw new CommonException('AppidIsNullError');
        }

        ['code' => $code,'anonymousCode' => $anonymousCode,'appid' => $appid] = $param;

        $systemDouyinConfigCount = SystemDouyinConfig::where('appid', $appid)->where('type', 10)->count();

        if (!$systemDouyinConfigCount) {
            throw new CommonException('SystemDouyinConfigIsNullError');
        }

        $systemDouyinConfigObject = SystemDouyinConfig::where('appid', $appid)->where('type', 10)->first();

        $systemDouyinConfigId = $systemDouyinConfigObject->id;

        $secret = $systemDouyinConfigObject->appsecret;


        $params = [
            'code' => $code,
            'appid' => $appid
        ];

        $config = [
            'appsecret' => $secret
        ];



        $result = DouYinLoginFacade::getOpenIdByCodeWithMiniProgram($params, $config);


        $collection = collect(['result' => $result,'appid' => $appid,'systemDouyinConfigId' => $systemDouyinConfigId]);

        return $collection;
    }
}
