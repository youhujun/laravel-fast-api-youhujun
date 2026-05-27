<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-20 11:31:50
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAlbumListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
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
        $userObject = $event->userObject;
        $isTransation = $event->isTransation;

        $cover_album_picture_uid = get_cover_album_picture_uid();

        $albumObject = ShardHelperFacade::createWithShard(Album::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'is_default' => 1,
            'album_name' => $userObject->account_name,
            'album_description' => $userObject->account_name,
            'sort' => 100,
            'cover_album_picture_uid' => $cover_album_picture_uid,
            'album_type' => 20,

        ]);

        if (!isset($albumObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserAlbumError');
        }

        $indexName = config('common_es.indices.album.albums');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $albumObject->album_uid,
			'shard_key'=>$albumObject->shard_key,
            'user_uid' => $albumObject->user_uid,
            'admin_uid'=>$albumObject->admin_uid,
            'album_uid' => $albumObject->album_uid,
            'shard_db' => ShardFacade::getDbName($albumObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumObject->user_uid, 'albums', $configKey),
            'is_default' => $albumObject->is_default,
            'is_system'=>$albumObject->is_system,
            'album_name' => $albumObject->album_name,
            'album_description' => $albumObject->album_description,
            'sort' => $albumObject->sort,
            'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
            'album_type' => $albumObject->album_type,
            'created_at'=>$albumObject->created_at,
            'updated_at'=>$albumObject->updated_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $dataArray['album_uid']);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加用户相册失败','$esResult' => $esResult], 'AddUserAlbumListener', 'handleError');
            
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsAddUserAlbumError');
        }
    }
}
