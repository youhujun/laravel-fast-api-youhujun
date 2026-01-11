<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:36:31
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Picture\Album;

/**
 * @see  \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperAdminAlbumListener
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
        $admin = $event->admin;
        $user = $event->user;
        $validated = $event->validated;

        $albumData = [];

        $albumData[] = [
            'admin_id'=>$admin->id,
            'user_id'=> 0,
            'is_default'=>1,
            'album_name'=>'admin_'.$user->account_name,
            'album_description'=>$user->account_name,
            'sort'=>100,
            'cover_album_picture_id'=>1,
            'album_type'=> 10,
            'created_at'=>date('Y-m-d H:i:s',time()),
            'created_time'=>time()
        ];

        $albumData[] = [
            'admin_id'=>0,
            'user_id'=> $user->id,
            'is_default'=>1,
            'album_name'=>'user_'.$user->account_name,
            'album_description'=>$user->account_name,
            'sort'=>100,
            'cover_album_picture_id'=>1,
            'album_type'=> 20,
            'created_at'=>date('Y-m-d H:i:s',time()),
            'created_time'=>time()
        ];

        $result = Album::insert($albumData);

        if(!$result)
        {
            throw new CommonException('AddDeveloperAlbumError');
        }

    }
}
