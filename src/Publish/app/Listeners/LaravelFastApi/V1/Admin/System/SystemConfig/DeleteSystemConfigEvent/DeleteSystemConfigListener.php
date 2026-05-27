<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-17 10:55:45
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 02:05:53
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\System\SystemConfig\DeleteSystemConfigEvent\DeleteSystemConfigListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\System\SystemConfig\DeleteSystemConfigEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\System\SystemConfig;


/**
 * @see \App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\DeleteSystemConfigEvent
 */
class DeleteSystemConfigListener
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
        $systemConfigObject = $event->systemConfigObject;
        $adminObject = $event->adminObject;

        $indexName = config('common_es.indices.system.system_configs');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($indexName, $systemConfigObject->id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新系统配置失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$systemConfigObject' => $systemConfigObject,'$adminObject' => $adminObject], 'DeleteSystemConfigListener', 'handleError');

            throw new CommonException('DeleteSystemConfigError');
        }
    }
}
