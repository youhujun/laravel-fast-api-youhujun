<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:32:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 14:46:29
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\Login\Common;

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

/**
 * @see \App\Facade\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacade
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
     * @param  Admin $admin
     */
    public function checkResetLogin($admin)
    {
        //注意登录时的admin 是数据库admin,所以即使token跟新了,但是redis的token还是原来的token
        $token = $admin->remember_token;

        //先清除redis中的缓存
        $this->clearAdminCache($admin, $token);

        //再更新数据库token
        $newToken = Str::random(60);

        $admin->setRememberToken($newToken);

        $newTokenResult = $admin->save();

        if(!$newTokenResult)
        {
            throw new CommonException('AdminLoginUpdateAdminTokenError');
        }

        //重新将数据存入到 redis中
        $this->loginCache($admin);

    }

    /**
     * 清除管理员缓存
     *
     * @param  Admin  $admin
     * @param  [String] $token
     */
    private function clearAdminCache($admin, $token)
    {
        Redis::del("admin_token:{$token}");
        Redis::hdel("admin:admin",$admin->id);
        Redis::hdel("admin_info:admin_info",$admin->id);
    }

    /**
     * 登录成功 redis 存储用户相关信息
     *
     * @param Admin $admin
     * @return void
     */
    private function loginCache($admin)
    {
        //用用户的rember_token 存储用户id 存储12小时
        $tokenResult = Redis::setex("admin_token:{$admin->remember_token}", 12 * 60 * 60, $admin->id);

        //根据用户id 存储用户信息

        //检测是否有用户信息了
        $hasResult = Redis::hget("admin:admin",$admin->id);

        //如果有就先删除
        if($hasResult)
        {
            Redis::hdel("admin:admin",$admin->id);
        }

        $redisResult = Redis::hset("admin:admin",$admin->id,serialize($admin));

        //存储成功以后 将remember_token 和 用户在 redis的id关系 存储 (因为上一个步骤需要用)
        if (!$tokenResult || !$redisResult)
        {
            throw new CommonException('RedisAddAdminError');
        }
    }
}
