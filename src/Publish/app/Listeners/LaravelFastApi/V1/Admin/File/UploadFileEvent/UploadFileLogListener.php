<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 13:37:50
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent\UploadFileLogListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminUploadFileLog;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\File\UploadFileEvent
 */
class UploadFileLogListener
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
        $uploadFileLogDataArray = $event->uploadFileLogDataArray;

        $adminObject = $event->adminObject;
        $logDataType = $event->logDataType;

        $uploadFileLogResult = null;

        //单文件
        if ($logDataType == 10) {
            $uploadFileLogResult = ShardHelperFacade::createWithShard(AdminUploadFileLog::class, $adminObject->biz_id, $uploadFileLogDataArray);
        }

        //多文件
        if ($logDataType == 20) {
            $uploadFileLogResult = ShardHelperFacade::insertBatchWithShard(AdminUploadFileLog::class, $uploadFileLogDataArray, 'admin_uid');
        }

        if (!$uploadFileLogResult) {
            throw new CommonException('UploadFileLogError');
        }
    }
}
