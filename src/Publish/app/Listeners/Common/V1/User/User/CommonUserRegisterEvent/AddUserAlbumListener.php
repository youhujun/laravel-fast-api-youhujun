<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:06
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-21 17:08:40
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\Picture\Album;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserAlbumListener
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
        $validated = $event->validated;
        $isTransation = $event->isTransation;

        //用户相册
        $album = new Album;

        $album ->user_id = $user->id;

        $album->created_at = time();

        $album->created_time = time();

        $album->is_default = 1;

        $album->album_name = $user->account_name;

        $album->album_description = $user->account_name;

        $album->sort = 100;

        $album->cover_album_picture_id = 1;

        //暂不保存 相册 先给一个用户相册
        $album->album_type = 20;

        //保存相册
        $albumResult = $album->save();

        if(!$albumResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }

            throw new CommonException('AddUserAlbumError');
        }
    }
}
