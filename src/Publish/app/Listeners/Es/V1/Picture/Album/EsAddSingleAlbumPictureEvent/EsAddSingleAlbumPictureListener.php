<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-24 00:45:14
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 01:01:26
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent\EsAddSingleAlbumPictureListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Common\CommonException;
use Illuminate\Support\Facades\DB;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;


/**
 * @see \App\Events\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent
 */
class EsAddSingleAlbumPictureListener
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
        $albumPictureObject = $event->albumPictureObject;
        $isTransation = $event->isTransation;

        $albumIndexName = config('common_es.indices.album.album_pictures');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $albumPictureObject->biz_id,
            'album_picture_uid' => $albumPictureObject->biz_id,
            'shard_key' => $albumPictureObject->shard_key,
            'shard_db' => ShardFacade::getDbName($albumPictureObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumPictureObject->user_uid, 'album_pictures', $configKey),
            'user_uid' => $albumPictureObject->user_uid,
            'admin_uid' => $albumPictureObject->user_uid,
            'album_uid' => $albumPictureObject->album_uid,
            'picture_name' => $albumPictureObject->picture_name,
            'picture_tag' => $albumPictureObject->picture_tag,
            'picture_path' => $albumPictureObject->picture_path,
            'picture_file' => $albumPictureObject->picture_file,
            'picture_size' => $albumPictureObject->picture_size,
            'picture_spec' => $albumPictureObject->picture_spec,
            'picture_type' => $albumPictureObject->picture_type,
            'created_at' => $albumPictureObject->created_at,
            'created_time' => $albumPictureObject->created_time,
            'updated_at' => $albumPictureObject->updated_at,
            'updated_time' => $albumPictureObject->updated_time,
            'deleted_at' => $albumPictureObject->deleted_at,
        ];

        $esAlbumPictureResult = EsFacade::createDoc($albumIndexName, $insertDataArray, $albumPictureObject->biz_id);

        if (!isset($esAlbumPictureResult['code']) || $esAlbumPictureResult['code'] != 0) {
            plog(['error' => 'es同步相册图片数据失败','$albumPictureObject' => $albumPictureObject,'$esAlbumPictureResult' => $esAlbumPictureResult], 'PhoneLoginWechatFacadeService', 'bindWeChatUserInfoError');

            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsSaveUserAvatarError');
        }

    }
}
