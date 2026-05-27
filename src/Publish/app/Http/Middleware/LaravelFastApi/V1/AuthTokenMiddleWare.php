<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-20 01:33:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 23:59:53
 * @FilePath: \youhu-laravel-api-12\app\Http\Middleware\LaravelFastApi\V1\AuthTokenMiddleWare.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Http\Middleware\LaravelFastApi\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\Common\CommonException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class AuthTokenMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $encryptedAuthToken = $request->header('X-Auth-Token');
        $timestamp = $request->header('X-Timestamp');
        $nonce = $request->header('X-Nonce');
        $service_flag = $request->header('X-Service');


        $isServiceVaild = $this->verifyServiceFlag($service_flag);

        if (!$isServiceVaild) {
            throw new CommonException('AuthServiceFlagError');
        }


        $isTImeValid = $this->verifyTimestamp((int)$timestamp);

        if (!$isTImeValid) {
            throw new CommonException('AuthTimeError');
        }

        $isNonceValid = $this->isNonceValid($nonce);

        if (!$isNonceValid) {
            throw new CommonException('AuthNonceError');
        }

        $isAuthTokenVaild = $this->verifyAuthToken($encryptedAuthToken, $request);

        if (!$isAuthTokenVaild) {
            throw new CommonException('AuthAccessTokenError');
        }

        // 将 header 数据合并到 request 输入中，这样 Validator::make($request->all()) 才能获取到
        $request->merge([
            'service_flag' => $service_flag,
        ]);

        return $next($request);
    }

    //验证标识
    public function verifyServiceFlag($service_flag)
    {
        $result = 0;

        $isYouHuBaseServiceFlagValid = hash_equals($service_flag, 'youhu-base');
        $isYouHuShopServiceFlagValid = hash_equals($service_flag, 'youhushop');
        $isYouHuServiceFlagValid = hash_equals($service_flag, 'youhu');
        $isXueHuServiceFlagValid = hash_equals($service_flag, 'xuehu');

        if ($isYouHuBaseServiceFlagValid  || $isYouHuShopServiceFlagValid || $isYouHuServiceFlagValid || $isXueHuServiceFlagValid) {
            $result =  1;
        }

        return $result;
    }

    //验证时间
    public function verifyTimestamp(int $timestamp, int $expireSeconds = 300): bool
    {
        $currentTime = time();
        return ($timestamp > $currentTime - $expireSeconds) &&
               ($timestamp < $currentTime + $expireSeconds);
    }

    // 服务器端缓存已使用的nonce(Redis缓存5分钟)
    public function isNonceValid(string $nonce): bool
    {
        Redis::select(4);
        $key = 'auth:nonce:' . $nonce;
        // 用SET NX（不存在则设置）+ EX（过期时间）的原子操作，替代先查后设
        // Redis::set返回true表示设置成功（nonce未使用），false表示已存在
        return  Redis::set($key, '1', 'EX', 300, 'NX');
    }

    public function verifyAuthToken($encryptedAuthToken, Request $request)
    {
        $reuslt = 0;
        Redis::select(6);

        //先查缓存
        $redisResult = Redis::hget('youhu-base:auth-token', $encryptedAuthToken);

        if ($redisResult) {
            $reuslt = 1;
        }

        //缓存没有查es


        $indexName = config('common_es.indices.youhu_auth_services');

        $queryArray = [
            'match' => ['auth_token' => $encryptedAuthToken]
        ];

        $esResult =  EsFacade::searchDoc($indexName, $queryArray);

        $hitNumber = 0;

        if (isset($esResult) && $esResult['code'] == 0) {
            if (isset($esResult['data']['hits']['total']['value'])) {
                $hitNumber = $esResult['data']['hits']['total']['value'];
            }
        }

        if (!$hitNumber) {
            $reuslt = 0;
            return $result;
        }

        Redis::hset('youhu-base:auth-token', $encryptedAuthToken, 1);

        $result = 1;

        return $result = 1;
    }
}
