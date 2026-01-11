<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-22 14:49:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-22 15:24:55
 * @FilePath: \app\Service\Facade\Common\V1\Login\CommonLoginFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Service\Facade\Common\V1\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * @see \App\Facade\Common\V1\Login\CommonLoginFacade
 */
class CommonLoginFacadeService
{
   public function test()
   {
       echo "CommonLoginFacadeService test";
   }

   /**
    *  单点登录 检测是否登录了,如果登录了,跟新token,让其他人重新登录
    *
    * @param  $user
    * @return void
    */
   public function checkResetLogin($user,$provider = 'user',$location = 'phone')
   {
        //注意登录时的user 是数据库user,所以即使token跟新了,但是redis的token还是原来的token
        $token = $user->remember_token;

        //先清除redis中的缓存
        $this->clearUserCache($user, $token,$provider,$location);

        //再更新数据库token
        $newToken = Str::random(60);

        $user->setRememberToken($newToken);

        $newTokenResult = $user->save();

        //重新将数据存入到 redis中
        $this->loginCache($user,$provider,$location);

   }

       /**
    * 清楚用户相关缓存
    *
    * @param User $user
    * @param [type] $token
    * @return void
    */
   public function clearUserCache($user,$token,$provider = 'user',$location = 'phone')
   {
       Redis::del("{$location}_{$provider}_token:{$token}");
       Redis::hdel("{$location}_{$provider}:{$provider}",$user->id);
       Redis::hdel("{$location}_{$provider}_info:{$provider}_info",$user->id);
   }

       /**
    * 登录成功 redis 存储用户相关信息
    *
    * @param $user
    * @return void
    */
   protected function loginCache($user,$provider,$location)
   {
        //存储用户 特别是 remember_token
        //根据用户id 存储 用户token  24小时
        $tokenResult = Redis::set("{$location}_{$provider}_token:{$user->remember_token}",$user->id);

        //检测是否有用户信息了
        $hasResult = Redis::hget("{$location}_{$provider}:{$provider}",$user->id);

        //如果有就先删除
        if($hasResult)
        {
            Redis::hdel("{$location}_{$provider}:{$provider}",$user->id);
        }

        //存储用户信息 然后获取 该用户在redis 的id
        $redisResult =  Redis::hset("{$location}_{$provider}:{$provider}",$user->id,convertToString($user));

        //存储成功以后 将remember_token 和 用户在 redis的id关系 存储 (因为上一个步骤需要用)
        if(!$tokenResult || !$redisResult )
        {
            throw new CommonException('RedisAddUserError');
        }

   }
}
