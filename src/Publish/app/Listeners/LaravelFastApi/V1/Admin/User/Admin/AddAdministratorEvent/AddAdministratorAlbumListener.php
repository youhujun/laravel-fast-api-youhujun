<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 11:33:47
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

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
        $loginAdminObject = $event->loginAdminObject;
        $adminObject = $event->adminObject;
        $requestDTO = $event->requestDTO;

        $esAlbumPictureIndexName = config('common_es.indices.album.album_pictures');

        $esAlbumPictureObject = EsQueryFacade::index($esAlbumPictureIndexName)->whereNull('deleted_at')->where('picture_tag', 'cover')->get()->first();

        if (!isset($esAlbumPictureObject->album_picture_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $insertAlbumDataArray = [
            'admin_uid' => $adminObject->biz_id,
            'user_uid' => $adminObject->user_uid,
            'cover_album_picture_uid' => $esAlbumPictureObject->album_picture_uid ?? 0,
            'is_default' => 1,
            'is_system' => 0,
            'album_type' => 10,
            'album_name' => $adminObject->account_name,
            'album_description' => $adminObject->account_name,
        ];

        $adminAlbumObject = ShardHelperFacade::createWithShard(Album::class, $adminObject->user_uid, $insertAlbumDataArray);

        if (!isset($adminAlbumObject->biz_id)) {
            throw new CommonException('AddAdminAlbumError');
        }

        $indexName = config('common_es.indices.album.albums');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $adminAlbumObject->album_uid,
			'shard_key'=>$adminAlbumObject->shard_key,
            'user_uid' => $adminAlbumObject->user_uid,
            'admin_uid'=>$adminAlbumObject->admin_uid,
            'album_uid' => $adminAlbumObject->album_uid,
            'shard_db' => ShardFacade::getDbName($adminAlbumObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($adminAlbumObject->user_uid, 'albums', $configKey),
            'is_default' => $adminAlbumObject->is_default,
            'is_system'=>$adminAlbumObject->is_system,
            'album_name' => $adminAlbumObject->album_name,
            'album_description' => $adminAlbumObject->album_description,
            'sort' => $adminAlbumObject->sort,
            'cover_album_picture_uid' => $adminAlbumObject->cover_album_picture_uid,
            'album_type' => $adminAlbumObject->album_type,
            'created_at'=>$adminAlbumObject->created_at,
            'updated_at'=>$adminAlbumObject->updated_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $adminAlbumObject->album_uid);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加管理员相册失败','$esResult' => $esResult], 'AddAdministratorAlbumListener', 'handleError');
            
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsAddAdministratorAlbumError');
        }
    }
}
