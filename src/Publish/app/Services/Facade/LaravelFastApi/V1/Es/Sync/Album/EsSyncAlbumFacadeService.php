<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-03 16:37:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-11 16:20:12
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\Album;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade
 */
class EsSyncAlbumFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncAlbumFacadeService test";
    }

    /**
     * 同步es相册
     */
    public function syncAlbums(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有albums数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.album.albums');

        Album::queryByAllShard()
        ->select(['album_uid','shard_key','admin_uid','user_uid','cover_album_picture_uid','is_default','is_system','album_type','album_name','album_description','sort','created_at', 'updated_at','deleted_at'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $albumCollection = $chunk;
            $esDataArray = $albumCollection->map(function ($albumObject) {
                $configKey = get_shard_config_key();

                return [
                    '_docId' => $albumObject->album_uid,
                    'album_uid' => $albumObject->album_uid,
                    'shard_key' => $albumObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($albumObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($albumObject->user_uid, 'albums', $configKey),
                    'admin_uid' => $albumObject->admin_uid,
                    'user_uid' => $albumObject->user_uid,
                    'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
                    'is_default' => $albumObject->is_default,
                    'is_system' => $albumObject->is_system,
                    'album_type' => $albumObject->album_type,
                    'album_name' => $albumObject->album_name,
                    'album_description' => $albumObject->album_description,
                    'sort' => $albumObject->sort,
                    'created_time' => $albumObject->created_time,
                    'updated_time' => $albumObject->updated_time,
                    'created_at' => $albumObject->created_at,
                    'updated_at' => $albumObject->updated_at,
                    'deleted_at' => $albumObject->deleted_at
                ];
            })->toArray();

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步相册数据失败','$result' => $result], 'EsSyncAlbumFacadeService', 'syncAlbumsError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步相册数据完成','total' => $total,'costTime' => $costTime], 'EsSyncAlbumFacadeService', 'syncAlbums');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有albums数据同步ES结束--2', 'info');
        }
    }

    /**
     * 同步相册图片
     */
    public function syncAlbumPictures(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有album_pictures数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.album.album_pictures');

        AlbumPicture::queryByAllShard()
        ->select(['album_picture_uid','admin_uid','user_uid','album_uid','picture_name','picture_tag','picture_path','picture_file','picture_size','picture_spec','picture_type','picture_url','created_at', 'updated_at','deleted_at'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $albumPictureCollection = $chunk;
            $esDataArray = $albumPictureCollection->map(function ($albumPictureObject) {
                $configKey = get_shard_config_key();

                return [
                    '_docId' => $albumPictureObject->album_picture_uid,
                    'shard_db' => ShardFacade::getDbName($albumPictureObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($albumPictureObject->user_uid, 'album_pictures', $configKey),
                    'album_picture_uid' => $albumPictureObject->album_picture_uid,
                    'album_uid' => $albumPictureObject->album_uid,
                    'admin_uid' => $albumPictureObject->admin_uid,
                    'user_uid' => $albumPictureObject->user_uid,
                    'picture_name' => $albumPictureObject->picture_name,
                    'picture_tag' => $albumPictureObject->picture_tag,
                    'picture_path' => $albumPictureObject->picture_path,
                    'picture_file' => $albumPictureObject->picture_file,
                    'picture_size' => $albumPictureObject->picture_size,
                    'picture_spec' => $albumPictureObject->picture_spec,
                    'picture_type' => $albumPictureObject->picture_type,
                    'picture_url' => $albumPictureObject->picture_url,
					'created_time' => $albumPictureObject->created_time,
                    'updated_time' => $albumPictureObject->updated_time,
                    'created_at' => $albumPictureObject->created_at,
                    'updated_at' => $albumPictureObject->updated_at,
                    'deleted_at' => $albumPictureObject->deleted_at
                ];
            })->toArray();

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步相册图片数据失败','$result' => $result], 'EsSyncAlbumFacadeService', 'syncAlbumPicturesError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步相册图片数据完成','total' => $total,'costTime' => $costTime], 'EsSyncAlbumFacadeService', 'syncAlbumPictures');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有album_pictures数据同步ES结束--2', 'info');
        }
    }
}
