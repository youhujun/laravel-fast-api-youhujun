<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:38:52
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-24 22:01:27
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserInfoListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserInfo;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserInfoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
     */
    public function handle(object $event): void
    {
        $user = $event->user;

		//p($user);die;

        $isTransation = $event->isTransation;

        //用户详情
        $userInfo = new UserInfo;

        //用户id
        $userInfo->user_id = $user->id;
        $userInfo->nick_name ='';
        $userInfo->real_name ='';
        $userInfo->id_number = null;
        $userInfo->solar_birthday_at = null;
        $userInfo->solar_birthday_time = 0;
        $userInfo->sex = 0;
        $userInfo->created_at = time();
        $userInfo->created_time = time();

        $userInfoResult = $userInfo ->save();

        if(!$userInfoResult)
        {
            if($isTransation)
            {
                 DB::rollBack();
            }

            throw new CommonException('AddUserInfoError');
        }
    }
}
