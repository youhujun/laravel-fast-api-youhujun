<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-11 09:21:39
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Service\Facade\LaravelFastApi\V1\Admin\Develop;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Facade\Admin\Develop\DeveloperFacade
 */
class DeveloperFacadeService
{
   public function test()
   {
       echo "DeveloperFacadeService test";
   }

   /**
    * 添加开发者
    *
    * @param [type] $validated
    * @return void
    */
   public function addDeveloper($admin,$validated)
   {
       /*  Array
        (
            [username] => youhu
            [password] => abc123
        ) */

        $result = code(config('admin_code.AddDeveloperError'));

        $user = new User;

		$user->userId = Str::uuid()->toString();

        $user->account_name = $validated['username'];

        $user->password = Hash::make($validated['password']);

        //用户级别 默认最低
        $user->level_id = 1;

        //用户默认未认证
        $user->real_auth_status = 10;
        //用户默认是可用的
        $user->switch = 1;
        //创建认证token
        $user->auth_token = Str::random(20);

        $user->created_at = time();
        $user->created_time = time();

        //保存用户
        $userResult = $user->save();

        $user_id = $user->id;

        $invite_code = $user_id;

        $length =  mb_strlen($invite_code);

        if($length < 4)
        {
            $invite_code = str_pad($invite_code,4,'0',STR_PAD_LEFT);
        }

        $user->invite_code = $invite_code;

        $userResult = $user->save();

        if(!$userResult)
        {
            throw new CommonException('AddDeveloperError');
        }

        AddDeveloperEvent::dispatch($admin,$user,$validated);

        CommonEvent::dispatch($admin,$validated,'AddDeveloper');

        $result = code(['code'=>0,'msg'=>'添加开发者成功!']);

        return $result;

   }
}
