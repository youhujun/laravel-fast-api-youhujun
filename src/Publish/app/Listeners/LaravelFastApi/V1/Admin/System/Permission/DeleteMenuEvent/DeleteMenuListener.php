<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 11:12:27
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-04 21:30:49
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuEvent\DeleteMenuListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuEvent
 */
class DeleteMenuListener
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
        
        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $esResult = EsFacade::updateDoc($esIndexName, $permissionObject->id, $updateDataArray);

      

        if (!isset($esResult['code']) || $esResult['code'] != 0) {

            if($isTransation){
               DB::rollBack();
            }

            plog(['error' => 'EsDeletePermissionError','$esResult' => $esResult,'$permissionObject' => $permissionObject,'$adminObject' => $adminObject], 'DeleteMenuListener', 'handleError');

            throw new CommonException('EsDeletePermissionError');
        }
    }
}
