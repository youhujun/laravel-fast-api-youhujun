<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 12:04:33
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\Picture\Album;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent
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
