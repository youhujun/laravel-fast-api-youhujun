<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 02:35:58
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Jobs\Common\V1\Es\Album\EsAddAdminAlbumJob;

/**
 * @see  \App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperAdminAlbumListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $userObject = $event->userObject;
        $validated = $event->validated;
        $isTransation = $event->isTransation;

        $adminObject = Admin::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        if (!isset($adminObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }
            throw new CommonException('AddDeveloperAdminError');
        }


        $cover_album_picture_uid = get_cover_album_picture_uid();

        $albumObject = ShardHelperFacade::createWithShard(Album::class, $userObject->biz_id, [
            'admin_uid' => $adminObject->admin_uid,
            'user_uid' => $userObject->biz_id,
            'is_default' => 1,
            'album_name' => $adminObject->account_name,
            'album_description' => $adminObject->account_name,
            'sort' => 100,
            'cover_album_picture_uid' => $cover_album_picture_uid,
            'album_type' => 10,
        ]);

        if (!isset($albumObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }
            throw new CommonException('AddDeveloperAlbumError');
        }

        EsAddAdminAlbumJob::dispatch($albumObject)->delay(now()->addSeconds(5));
    }
}
