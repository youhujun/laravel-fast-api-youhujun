<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-22 21:30:41
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent\UploadFileLogListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\File\UploadFileEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Admin\CommonException;

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
        $uploadFileLogData = $event->uploadFileLogData;

        $logDataType = $event->logDataType;

        //保存日志

        //单文件
        if($logDataType == 10)
        {
            $uploadFileLog = new AdminUploadFileLog;

            foreach ($uploadFileLogData as $key => $value)
            {
                $uploadFileLog->$key = $value;
            }

            $uploadFileLog->created_at = time();
            $uploadFileLog->created_time = time();

            $uploadFileLogResult = $uploadFileLog->save();

        }

        //多文件
        if($logDataType == 20)
        {

            foreach ($uploadFileLogData as $key => &$item)
            {
               $item['created_at'] = date('Y-m-d H:i:s',time());
               $item['created_time'] = time();
            }

            $uploadFileLogResult = AdminUploadFileLog::insert($uploadFileLogData);

        }

        if(!$uploadFileLogResult)
        {
            throw new CommonException('UploadFileLogError');
        }

    }
}
