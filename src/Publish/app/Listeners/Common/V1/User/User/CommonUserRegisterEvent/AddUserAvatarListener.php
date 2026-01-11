<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-21 17:08:26
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAvatarListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;

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
        $user = $event->user;

        $isTransation = $event->isTransation;

        //用户头像
        $userAvatar = new UserAvatar;

        $userAvatar->user_id = $user->id;

        $userAvatar->album_picture_id = 2;

        $userAvatar->created_at = time();

        $userAvatar->created_time = time();

        $userAvatarResult = $userAvatar->save();

        if(!$userAvatarResult)
        {
            if($isTransation)
            {
                 DB::rollBack();
            }
            throw new CommonException('AddUserAvatarError');
        }
    }
}
