<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-04 03:11:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 14:59:26
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Api\Request\ApiRequestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 *
 * 使用示例:
 *
$url = config('shardmap_api_url.SyncMapData');

$params = ['shardInfo' => $shardInfo,'modelData' => ['account_name' => $userObject->account_name]];

ApiRequestFacade::decoder($userUid, $url, $params, $originServiceFlag = 'youhu-base');

 */

namespace App\Services\Facade\Common\V1\Api\Request;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Facades\Common\V1\Api\Request\ApiRequestFacade
 */
class ApiRequestFacadeService
{
    public function test()
    {
        echo "ApiRequestFacadeService test";
    }

    /**
    * Create the event listener.
    */
    public function __construct()
    {

    }

    /**
     * 解码并执行API请求
     *
     * 根据用户UID和服务标识获取认证信息，对请求参数进行AES加密，
     * 构建包含认证令牌、随机数和时间戳的请求头，发送POST请求并返回结果
     *
     * @param string $user_uid 用户唯一标识
     * @param string $url 请求的API地址
     * @param array $paramsArray 请求参数数组，每个参数值将被AES加密
     * @param string $originServiceFlag 服务标识，默认为'youhu-base'
     * @return string API请求的原始响应结果
     * @throws CommonException 当服务标识为空时抛出异常
     */
    public function decoder(string $user_uid, string $url, array $paramsArray, string $originServiceFlag = 'youhu-base')
    {
        if (!$originServiceFlag || empty($originServiceFlag)) {
            throw new CommonException('ServiceFlagError');
        }

        $indexName = config('common_es.indices.youhu_auth_services');

        $esResult = EsFacade::findDoc($indexName, $user_uid);

        if (!isset($esResult['code']) || !$esResult['code'] == 0 || !isset($esResult['data'])) {
            throw new CommonException('ServiceEsDataError');
        }

        $youhuAuthServiceArray = $esResult['data'];

        $encryptedSecretKey = $youhuAuthServiceArray['secret_key'];
        $encryptedAuthToken = $youhuAuthServiceArray['auth_token'];

        $aesKey = config('common.aes.key');

        $secretKey = AESFacade::decrypt($encryptedSecretKey, $aesKey);

        $nonce = bin2hex(random_bytes(8));
        $timestamp = time();

        //处理header
        $headers = ['X-Auth-Token:'.$encryptedAuthToken,"X-Service:{$originServiceFlag}",'X-Nonce:'.$nonce,'X-Timestamp:'.$timestamp];

        //处理params
        foreach ($paramsArray as $key => &$value) {
            $value = AESFacade::encrypt(json_encode($value), $secretKey);
        }

        $paramsArray['user_uid'] = AESFacade::encrypt($user_uid, $aesKey);

        plog(['info' => '请求记录日志','$url' => $url,'$headers' => $headers,'$params' => $paramsArray], $originServiceFlag, 'ApiRequest');

        $result = http_post($url, $headers, $paramsArray);

        // 解析JSON字符串为对象，避免双重编码导致Unicode转义
        $resultData = json_decode($result, true);

        plog(['info' => '请求记录日志结果','$result' => $resultData], $originServiceFlag, 'ApiRequest');

        return $result;
    }
}
