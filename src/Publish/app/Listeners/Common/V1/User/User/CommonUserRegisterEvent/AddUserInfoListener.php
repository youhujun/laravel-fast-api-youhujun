<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:38:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:06:48
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserInfoListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
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
        $userObject = $event->userObject;
        $businessRegisterUserDTO = $event->businessRegisterUserDTO;
        $isTransation = $event->isTransation;

        $userInfoObject = ShardHelperFacade::createWithShard(UserInfo::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'nick_name' => isset($businessRegisterUserDTO->nick_name) ? $businessRegisterUserDTO->nick_name : '',
            'real_name' => '',
            'id_number' => null,
            'solar_birthday_at' => null,
            'solar_birthday_time' => 0,
            'sex' => isset($businessRegisterUserDTO->sex) ? $businessRegisterUserDTO->sex : 0
        ]);

        if (!isset($userInfoObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserInfoError');
        }
    }
}
