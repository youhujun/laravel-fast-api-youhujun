<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 10:38:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 20:36:30
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Api\Test\TestController.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Api\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Date;
use App\Exceptions\Api\CommonException;
use App\DTOs\Api\V1\TestDTO;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Jobs\Api\V1\Log\ApiEventLogJob;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $result = code(\config('common_code.AuthTokenError'));

        plog(['info' => '请求数据','$request' => $request->all()], 'TestController', 'test');

        $testDTO = (new TestDTO())->validate($request->all());

        plog(['info' => '记录dto','$testDTO' => $testDTO], 'TestController', 'test');


        $validated = $testDTO->toArray();

        $encrypted_user_uid = $validated['user_uid'];
        $encrypted_params = $validated['params'];
        $service_flag = $validated['service_flag'];

        //p($validated);

        $aesKey = config('common.aes.key');

        $user_uid = AESFacade::decrypt($encrypted_user_uid, $aesKey);

        $indexName = config('common_es.indices.youhu_auth_services');

        $esResult = EsFacade::findDoc($indexName, $user_uid);

        //p($esResult);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            throw new CommonException('EsSelectError');
        }

        $youhuAuthServiceArray = $esResult['data'];

        $encrypted_secret_key = $youhuAuthServiceArray['secret_key'];

        $secret_key = AESFacade::decrypt($encrypted_secret_key, $aesKey);

        $paramsJsonString = AESFacade::decrypt($encrypted_params, $secret_key);

        $params = json_decode($paramsJsonString, true);

        plog(['info' => '解密数据','params' => $params,'user_uid' => $user_uid,'service_flag' => $service_flag], 'TestController', 'test');

        ApiEventLogJob::dispatch($user_uid, $service_flag, json_encode($params, JSON_UNESCAPED_UNICODE))->delay(Date::now()->addSeconds(3));

        $result = ['code' => 0, 'msg' => '测试成功'];

        return $result;
    }
}
