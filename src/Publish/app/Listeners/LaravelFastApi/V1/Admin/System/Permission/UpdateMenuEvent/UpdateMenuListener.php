<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 11:10:24
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-04 21:26:25
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent\UpdateMenuListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuEvent
 */
class UpdateMenuListener
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
        $permissionObject =  $event->permissionObject;
        $adminObject = $event->adminObject;
        $isTransation = $event->isTransation;

        $esIndexName = config('common_es.indices.system.permissions');

        $queryArray = [
            'match_all' => new \stdClass()
        ];

        $deleteEsResult = EsFacade::deleteByQuery($esIndexName, $queryArray);

        if (!isset($deleteEsResult['code']) || $deleteEsResult['code'] != 0) {
            if($isTransation){
               DB::rollBack();
            }
            plog(['error' => 'EsUpdatePermissionError','$deleteEsResult' => $deleteEsResult,'$permissionObject' => $permissionObject,'$adminObject' => $adminObject], 'UpdateMenuListener', 'handleError');

            throw new CommonException('EsUpdatePermissionError');
        }

        //执行同步
        EsSyncSystemFacade::syncPermission();
    }
}
