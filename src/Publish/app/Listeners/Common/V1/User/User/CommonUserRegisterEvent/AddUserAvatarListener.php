<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:24
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-11 02:51:33
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAvatarListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserAvatarListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $userObject = $event->userObject;
        $isTransation = $event->isTransation;

        $album_picture_uid = get_avatar_album_picture_uid();

        $userAvatarObject = ShardHelperFacade::createWithShard(UserAvatar::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'album_picture_uid' => $album_picture_uid,
            'is_default' => 1,
        ]);

        if (!isset($userAvatarObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }
            throw new CommonException('AddUserAvatarError');
        }
    }
}
