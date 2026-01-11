<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:39:41
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAvatarListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;

/**
 * @see \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperUserAvatarListener
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
        $admin = $event->admin;
        $validated = $event->validated;

        //用户头像
        $userAvatar = new UserAvatar;

        $userAvatar->user_id = $user->id;

        $userAvatar->album_picture_id = 2;

        //设置为默认头像
        $userAvatar->is_default = 1;

        $userAvatar->created_at = time();
        $userAvatar->created_time = time();

        $result = $userAvatar->save();

        if(!$result)
        {
            throw new CommonException('AddDeveloperAvatarError');
        }
    }
}
