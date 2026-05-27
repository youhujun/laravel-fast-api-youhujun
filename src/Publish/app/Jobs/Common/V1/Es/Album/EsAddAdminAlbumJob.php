<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-05 10:50:40
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 11:09:59
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\V1\Es\Album\EsAddAdminAlbumJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\V1\Es\Album;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use App\Models\LaravelFastApi\V1\Picture\Album;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

class EsAddAdminAlbumJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Album $albumObject;
    /**
     * 任务尝试次数
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * 任务失败前允许的最大异常数
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * 如果任务的模型不再存在，则删除该任务
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;
    /**
     * Create a new job instance.
     */
    public function __construct(Album $albumObject)
    {
        $this->albumObject = $albumObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $albumObject = $this->albumObject;

        $indexName = config('common_es.indices.album.albums');

        $configKey = get_shard_config_key();

        $dataArray = [
            '_docId' =>  $albumObject->album_uid,
            'shard_key' => $albumObject->shard_key,
            'admin_uid' => $albumObject->admin_uid,
            'user_uid' => $albumObject->user_uid,
            'album_uid' => $albumObject->album_uid,
            'shard_db' => ShardFacade::getDbName($albumObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($albumObject->user_uid, 'albums', $configKey),
            'is_default' => $albumObject->is_default,
            'album_name' => $albumObject->album_name,
            'album_description' => $albumObject->album_description,
            'sort' => $albumObject->sort,
            'cover_album_picture_uid' => $albumObject->cover_album_picture_uid,
            'album_type' => $albumObject->album_type,
        ];

        $esResult = EsFacade::createDoc($indexName, $dataArray, $dataArray['album_uid']);

        plog(['info' => 'es添加管理员呢相册完成','$esResult' => $esResult], 'EsAddAdminAlbumJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加管理员相册失败','$esResult' => $esResult], 'EsAddAdminAlbumJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
