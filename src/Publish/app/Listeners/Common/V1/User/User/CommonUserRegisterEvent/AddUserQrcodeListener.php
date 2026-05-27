<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-23 17:52:37
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Pub\V1\Qrcode\PubQrcodeFacade;
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\User\User;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent
 */
class AddUserQrcodeListener
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

        //第一步先生成二维码
        PubQrcodeFacade::makeQrcdoeWithUser($userObject);
        //第二步 存入图片
        $albumObject = Album::queryByShard($userObject->user_uid)->where('album_type', 20)->where('is_default', 1)->first();

        if (!isset($albumObject->biz_id)) {
            throw new CommonException('AlbumNotFoundError');
        }


        $albumPictureObject = ShardHelperFacade::createWithShard(AlbumPicture::class, $userObject->user_uid, [
            'user_uid' => $userObject->user_uid,
            'album_uid' => $albumObject->biz_id,
            'picture_name' => "{$userObject->user_uid}_qrcode",
            'picture_path' => "/user/album/{$userObject->user_uid}/",
            'picture_size' => 30,
            'picture_spec' => "300x300",
            'picture_tag' => 'qrcode',
            'picture_file' => "{$userObject->user_uid}_qrcode.png",
            'picture_type' => 10
        ]);

        if (!isset($albumPictureObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('SaveUserQrcodeError');
        }

        //第三步,存入二维码
        $userQrcodeObject = ShardHelperFacade::createWithShard(UserQrcode::class, $userObject->user_uid, [
            'user_uid' => $userObject->user_uid,
            'album_picture_uid' => $albumPictureObject->biz_id,
            'is_default' => 1,
            'sort' => 100,
        ]);

        if (!isset($userQrcodeObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserQrcodeError');
        }

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
            plog(['error' => 'es同步相册图片数据失败','$albumPictureObject' => $albumPictureObject,'$esAlbumPictureResult' => $esAlbumPictureResult], 'AddUserQrcodeListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }

             throw new CommonException('EsSaveUserQrcodeError');
        }

    }
}
