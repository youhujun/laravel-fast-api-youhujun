<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-05 02:27:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 02:42:24
 * @FilePath: \youhu-laravel-api-12\app\Jobs\Common\V1\Es\Album\EsUpdateAlbumPictureJob.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Jobs\Common\V1\Es\Album;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\RateLimited;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class EsUpdateAlbumPictureJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected AlbumPicture $albumPictureObject;
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
    public function __construct(AlbumPicture $albumPictureObject)
    {
        $this->albumPictureObject = $albumPictureObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $albumPictureObject = $this->albumPictureObject;
        $indexName = config('common_es.indices.album.album_pictures');

        $dataArray = [
            'admin_uid' => $albumPictureObject->admin_uid,
            'user_uid' => $albumPictureObject->user_uid,
            'album_uid' => $albumPictureObject->album_uid,
            'picture_name' => $albumPictureObject->picture_name,
            'picture_file' => $albumPictureObject->picture_file,
            'picture_path' => $albumPictureObject->picture_path,
            'picture_size' => $albumPictureObject->picture_size,
            'picture_spec' => $albumPictureObject->picture_spec,
            'picture_type' => $albumPictureObject->picture_type,
            'picture_url' => $albumPictureObject->picture_url,
            'created_at' => $albumPictureObject->created_at,
            'updated_at' => $albumPictureObject->updated_at,
            'deleted_at' => $albumPictureObject->deleted_at,
        ];
        $esResult = EsFacade::updateDoc($indexName, $albumPictureObject->album_picture_uid, $dataArray);

        plog(['info' => 'es更新相册图片完成','$esResult' => $esResult], 'EsUpdateAlbumpictureJob', 'handle');

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新相册图片失败','$esResult' => $result], 'EsUpdateAlbumpictureJob', 'handleError');
        }
    }

    public function middleware()
    {
        return [new RateLimited()];
    }
}
