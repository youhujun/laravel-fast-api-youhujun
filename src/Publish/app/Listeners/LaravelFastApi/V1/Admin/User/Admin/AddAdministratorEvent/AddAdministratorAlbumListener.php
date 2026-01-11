<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-16 09:03:10
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\LaravelFastApi\V1\Picture\Album;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent
 */
class AddAdministratorAlbumListener
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
        $addAdmin = $event->addAdmin;
        $validated = $event->validated;

        $albumData = [];

        $albumData[] = [
            'admin_id'=> $addAdmin->id,
            'is_default'=>1,
            'album_name'=>'admin_'.$addAdmin->account_name,
            'album_description'=>'admin_'.$addAdmin->account_name,
            'sort'=>100,
            'cover_album_picture_id'=>1,
            'album_type'=> 10,
            'created_at'=>date('Y-m-d H:i:s',time()),
            'created_time'=>time()
        ];

        $albumResult = Album::insert($albumData);

        if(!$albumResult)
        {
            throw new CommonException('AddAdminAlbumError');
        }
    }
}
