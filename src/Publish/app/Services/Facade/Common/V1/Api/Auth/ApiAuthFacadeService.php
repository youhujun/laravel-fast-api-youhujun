<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-20 21:34:03
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 02:10:40
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Api\Auth\ApiAuthFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\Api\Auth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\AuthSignFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;

/**
 * @see \App\Facades\Common\V1\Api\Auth\ApiAuthFacade
 */
class ApiAuthFacadeService
{
    public function test()
    {
        echo "ApiAuthFacadeService test";
    }

    /**
     * 获取临时凭证
     *
     * @param  [type] $url
     * @param  [type] $serviceFlag
     */
    public function getAccessToken($url, $serviceFlag)
    {
        $preParams = [
            'service_flag' => $serviceFlag,
        ];

        $preResult = $this->prepareSign($preParams);

        extract($preResult);

        plog([
            '$params' => $params,
            '$headers' => $headers,
            '$url' => $url,
            'info' => 'getAccessToken'
        ], 'ApiAuth', 'GetAccessToken');

        $result = http_post($url, $headers, $params);

        return $result;
    }

    /**
     * 验证签名
     *
     * @param  [type] $validated
     */
    public function verifySign($validated)
    {
        extract($validated);

        unset($validated['sign']);

        $encryptedSecretKey = Cache::get('verifySign.secretKey');

        if (!$encryptedSecretKey) {
            clean_system_config();
            make_system_config();
            $encryptedSecretKey = Cache::get('verifySign.secretKey');
        }

        $aesKey = config('common.aes.key');

        $decryptedSecretKey = AESFacade::decrypt($encryptedSecretKey, $aesKey);

        $generatedSign = AuthSignFacade::makeSign($validated, $decryptedSecretKey);

        if (config('common.debug.verify_sign_log', false)) {
            // 调试日志
            plog([
                '$sign' => $sign,
                '$generatedSign' => $generatedSign,
                '$validated' => $validated,
                'info' => 'verifySign'
            ], 'ApiAuth', 'VerifySign');
        }

        $isValid = hash_equals($generatedSign, $sign);

        if (!$isValid) {
            throw new CommonException('VerifySignError');
        }
    }


    /**
     * 准备签名
     *
     * @param  array $preParams
     */
    private function prepareSign(array $preParams)
    {
        $nonce = bin2hex(random_bytes(8));
        $timestamp = time();

        extract($preParams);

        if (!isset($service_flag)) {
            throw new CommonException('ServiceFlagEmptyError');
        }
        // 签名时需要包含所有参数：user_uid, timestamp, nonce
        $singParams = [
            'timestamp' => $timestamp,
            'nonce' => $nonce
        ];

        $params = array_merge($singParams, $preParams);

        $encryptedSecretKey = Cache::get('verifySign.secretKey');

        if (!$encryptedSecretKey) {
            clean_system_config();
            make_system_config();
            $encryptedSecretKey = Cache::get('verifySign.secretKey');
        }

        $aesKey = config('common.aes.key');

        $decryptedSecretKey = AESFacade::decrypt($encryptedSecretKey, $aesKey);

        $sign = AuthSignFacade::makeSign($params, $decryptedSecretKey);

        $headers = ['X-Service:'.$service_flag,'X-Nonce:'.$nonce,'X-Timestamp:'.$timestamp,'X-Sign:'.$sign];

        return ['params' => $params,'headers' => $headers];
    }


    /**
     * 获取临时凭证
     *
     * @param  [type] $bizPrefix
     */
    public function getTempToken($bizPrefix)
    {
        $accessToken = $this->generateSecureTempCredential($bizPrefix);

        Redis::select(5);
        $key = 'auth:access_token:' . $accessToken;
        // 用SET NX（不存在则设置）+ EX（过期时间）的原子操作，替代先查后设
        // Redis::set返回true表示设置成功（nonce未使用），false表示已存在
        Redis::set($key, '1', 'EX', 180, 'NX');

        return $accessToken;
    }


    /**
     * 生成高安全级别的唯一临时凭证（适配PHP8.0+）
     * 核心：UUIDv4（纯随机，无规律）+ 毫秒时间戳 + 业务秘钥 + 加盐哈希
     * 防伪造能力：UUID无规律+秘钥仅服务端知晓+哈希不可逆，几乎无法伪造
     * @param string $bizPrefix 业务前缀（区分不同场景的凭证，如order/pay/user）
     * @return string 32位固定长度的安全临时凭证
     */
    private function generateSecureTempCredential(string $bizPrefix = 'temp_cred_'): string
    {
        // 1. 生成PHP8.0原生UUIDv4（RFC4122标准，纯随机，全局唯一）
        // UUIDv4是目前最安全的随机UUID类型，无任何时间/机器特征，无法猜测
        $uuid = uuid_create(UUID_TYPE_RANDOM);

        // 2. 获取毫秒级时间戳（精准到微秒，避免同一毫秒重复）
        $microTimestamp = (string)(microtime(true) * 1000000);

        $encryptedSecretKey = Cache::get('verifySign.secretKey');

        $aesKey = config('common.aes.key');

        $decryptedSecretKey = AESFacade::decrypt($encryptedSecretKey, $aesKey);

        // 3. 读取服务端专属秘钥（存在.env/config中，仅服务端知晓）
        // $secretKey = config('youhu.sign_secret') ?? 'default_youhu_secret_2026';

        // 4. 加盐混合：UUID + 时间戳 + 秘钥（多层混合，提升伪造难度）
        $mixStr = $uuid . '_' . $microTimestamp . '_' . $decryptedSecretKey;

        // 5. 使用sha256哈希（比md5更安全，再转md5是为了保持32位短格式）
        // 也可以直接返回sha256的64位结果，安全性更高
        $credential = hash('sha256', $mixStr);
        // 如需固定32位格式，用这行替代上面：$credential = md5(hash('sha256', $mixStr));

        // 6. 拼接业务前缀，便于识别凭证归属
        return $bizPrefix . $credential;
    }
}
