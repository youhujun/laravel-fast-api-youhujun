<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:32:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 00:18:55
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Login\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacade
 */
class AdminBackgroundLoginFacadeService
{
    public function test()
    {
        echo "AdminBackgroundLoginFacadeService test";
    }

    /**
      * 为了单点登录,执行一边重新再登录逻辑,这样其他设备登录的会自动退出
      *
      * @param  Admin $adminObject
      */
    public function checkResetLogin(Admin $adminObject): void
    {
        //注意登录时的admin 是数据库admin,所以即使token跟新了,但是redis的token还是原来的token
        $token = $adminObject->remember_token;

        //先清除redis中的缓存
        $this->clearAdminCache($adminObject, $token);

        //再更新数据库token
        $newToken = Str::random(60);

        $adminObject->setRememberToken($newToken);

        $newTokenResult = $adminObject->save();

        if (!$newTokenResult) {
            throw new CommonException('AdminLoginUpdateAdminTokenError');
        }

        //重新将数据存入到 redis中
        $this->loginCache($adminObject);
    }

    /**
     * 清除管理员缓存
     *
     * @param  Admin  $adminObject
     * @param  [String] $token
     */
    private function clearAdminCache(Admin $adminObject, $token): void
    {
        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminInfoKey = config('common_redis.admin_info.key');

        $redisAdminField = config('common_redis.admin.field');
        $redisAdminInfoField = config('common_redis.admin_info.field');

        Redis::del($redisAdminTokenKey.$token);
        Redis::hdel($redisAdminKey, $redisAdminField.$adminObject->biz_id);
        Redis::hdel($redisAdminInfoKey, $redisAdminInfoField.$adminObject->biz_id);
    }

    /**
     * 登录成功 redis 存储用户相关信息
     *
     * @param Admin $adminObject
     * @return void
     */
    private function loginCache(Admin $adminObject): void
    {
        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminField = config('common_redis.admin.field');
        $redisLoginTime = config('common_redis.ttl.login');

        //用用户的rember_token 存储用户id 存储12小时
        $tokenResult = Redis::setex($redisAdminTokenKey.$adminObject->remember_token, $redisLoginTime, $adminObject->biz_id);

        //根据用户id 存储用户信息
        //检测是否有用户信息了
        $hasResult = Redis::hget($redisAdminKey, $redisAdminField.$adminObject->biz_id);

        //如果有就先删除
        if ($hasResult) {
            Redis::hdel($redisAdminKey, $redisAdminField.$adminObject->biz_id);
        }

        $redisResult = Redis::hset($redisAdminKey, $redisAdminField.$adminObject->biz_id, serialize($adminObject));

        //存储成功以后 将remember_token 和 用户在 redis的id关系 存储 (因为上一个步骤需要用)
        if (!$tokenResult || !$redisResult) {
            throw new CommonException('RedisAddAdminError');
        }
    }
}
