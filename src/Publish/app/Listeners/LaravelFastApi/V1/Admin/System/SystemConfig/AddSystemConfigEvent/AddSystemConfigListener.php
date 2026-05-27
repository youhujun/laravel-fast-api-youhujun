<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-17 10:54:44
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 02:04:21
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\System\SystemConfig\AddSystemConfigEvent\AddSystemConfigListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\System\SystemConfig\AddSystemConfigEvent;

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
 * @see \App\Events\LaravelFastApi\V1\Admin\System\SystemConfig\AddSystemConfigEvent
 */
class AddSystemConfigListener
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

        $insertDataArray = [
            '_docId' => $systemConfigObject->id,
            'id' => $systemConfigObject->id,
            'parent_id' => $systemConfigObject->parent_id,
            'deep' => $systemConfigObject->deep,
            'item_type' => $systemConfigObject->item_type,
            'item_label' => $systemConfigObject->item_label,
            'item_value' => $systemConfigObject->item_value,
            'item_price' => $systemConfigObject->item_price,
            'item_path' => $systemConfigObject->item_path,
            'item_introduction' => $systemConfigObject->item_introduction,
            'sort' => $systemConfigObject->sort,
            'created_time' => $systemConfigObject->created_time,
            'updated_time' => $systemConfigObject->updated_time,
            'created_at' => $systemConfigObject->created_at,
            'updated_at' => $systemConfigObject->updated_at,
            'deleted_at' => $systemConfigObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $systemConfigObject->id);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {

            plog(['error' => 'es添加系统配置失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$systemConfigObject' => $systemConfigObject,'$adminObject' => $adminObject], 'AddSystemConfigListener', 'handleError');

            throw new CommonException('AddSystemConfigError');
        }
    }
}
