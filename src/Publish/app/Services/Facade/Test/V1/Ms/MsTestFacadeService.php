<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer & codebuddy
 * @Date: 2026-03-30 00:09:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 23:49:12
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\Ms\MsTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer & codebuddy. All rights reserved.
 */

namespace App\Services\Facade\Test\V1\Ms;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Api\Auth\ApiAuthFacade;
use App\Facades\Common\V1\Api\Request\ApiRequestFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;

/**
 * @see \App\Facades\Test\V1\Ms\MsTestFacade
 */
class MsTestFacadeService
{
    public function __construct()
    {
        
    }
    public function test()
    {
        echo "MsTestFacadeService test";

        //$this->testGetAccessToken();
        $this->testMsApi();
    }

    /**
     * 测试获取访问令牌
     */
    public function testGetAccessToken()
    {
        //$url = config('youhu_api_url.GetAccessToken');
        //$url = config('youhushop_api_url.GetAccessToken');
        // $url = config('xuehu_api_url.GetAccessToken');
        $url = config('youhubase_api_url.GetAccessToken');

        $accessTokenResult  = ApiAuthFacade::getAccessToken($url, 'youhu-base');

        $accessTokenArray = json_decode($accessTokenResult, true);

        $access_token = $accessTokenArray['access_token'];

        p($access_token);
    }

    /**
     * 测试微服务通信
     */
    public function testMsApi()
    {
        $user_uid = '';
        $indexName = config('common_es.indices.user.users');

        $queryArray = [
            'match' => ['account_name' => 'develop']
        ];

        $result = EsFacade::searchDoc($indexName, $queryArray);

        if (isset($result['code']) && $result['code'] == 0 && isset($result['data']['hits']['total']['value'])) {
            if ($result['data']['hits']['total']['value'] > 0) {
                $userArray = $result['data']['hits']['hits'][0]['_source'];

                $user_uid = $userArray['user_uid'];
            }
        }

        //$url = config('youhubase_api_url.test');
        //$url = config('youhu_api_url.test');
        // $url = config('youhushop_api_url.test');
        $url = config('xuehu_api_url.test');


        $paramsArray = ['params' => '测试API通信'];

        //$result = ApiRequestFacade::decoder($user_uid, $url, $paramsArray, $originServiceFlag = 'youhu-base');
        //$result = ApiRequestFacade::decoder($user_uid, $url, $paramsArray, $originServiceFlag = 'youhu');
        //$result = ApiRequestFacade::decoder($user_uid, $url, $paramsArray, $originServiceFlag = 'youhushop');
        $result = ApiRequestFacade::decoder($user_uid, $url, $paramsArray, $originServiceFlag = 'xuehu');

        p($result);
    }
}
