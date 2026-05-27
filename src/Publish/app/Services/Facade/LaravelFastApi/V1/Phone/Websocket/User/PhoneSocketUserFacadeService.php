<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-16 18:51:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 16:05:16
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Facades\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacade
 */
class PhoneSocketUserFacadeService
{
   public function test()
   {
       echo "PhoneSocketUserFacadeService test";
   }


   /**
    * 检测socket 用户是否登录
    *
    * @param  [type] $user_uid
    * @param  [type] $token
    */
   public function checkSocketUserIsLogin($user_uid,$token)
   {
        $checkResult = 0;

        //Log::debug(['$user_uid'=>$user_uid,'token'=>$token]);

        if(isset($user_uid))
        {
            $userObject = User::find($user_uid);

            // Log::debug(['$userObject'=>$userObject]);

            if($userObject->remember_token == $token)
            {
                $checkResult = 1;
                Log::debug(['$user_uid'=>$user_uid.'-socketLoginSuccess',]);
            }
        }

        return $checkResult;

   }

   /**
    * 保存socket用户
    *
    * @param  [type] $user_uid
    * @param  [type] $frameId
    */
   public function saveSocketUser($user_uid,$frameFd)
   {
        $result = 0;

        if(Redis::hget('socket:socket',$user_uid))
        {
            Redis::hdel('socket:socket',$user_uid);
        }

        $result = Redis::hset('socket:socket',$user_uid,$frameFd);

        //Log::debug(['$redisResult'=>$user_uid.'-'.$result,]);

        return $result;
   }

   /**
    * 删除socket用户
    *
    * @param  [type] $user_uid
    */
   public function deleteSocketUser($user_uid)
   {
        Redis::hdel('socket:socket',$user_uid);
   }
}
