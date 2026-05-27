<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 10:46:27
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-04 14:48:13
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\System\Permission\AddMenuEvent\AddMenuListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\System\Permission\AddMenuEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\System\Permission\AddMenuEvent
 */
class AddMenuListener
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

        $insertDataArray = [
            '_docId' => $permissionObject->id,
            'id' => $permissionObject->id,
            'parent_id' => $permissionObject->parent_id,
            'deep' => $permissionObject->deep,
            'type' => $permissionObject->type,
            'route_name' => $permissionObject->route_name,
            'route_path' => $permissionObject->route_path,
            'component' => $permissionObject->component,
            'hidden' => $permissionObject->hidden,
            'always_show' => $permissionObject->always_show,
            'redirect' => $permissionObject->redirect,
            'permission_tag' => $permissionObject->permission_tag,
            'meta_title' => $permissionObject->meta_title,
            'meta_icon' => $permissionObject->meta_icon,
            'meta_no_cache' => $permissionObject->meta_no_cache,
            'meta_affix' => $permissionObject->meta_affix,
            'meta_breadcrumb' => $permissionObject->meta_breadcrumb,
            'meta_active_menu' => $permissionObject->meta_active_menu,
            'switch' => $permissionObject->switch,
            'sort' => $permissionObject->sort,
            'created_time' => $permissionObject->created_time,
            'updated_time' => $permissionObject->updated_time,
            'created_at' => $permissionObject->created_at,
            'updated_at' => $permissionObject->updated_at,
            'deleted_at' => $permissionObject->deleted_at,

        ];

        $esResult = Esfacade::createDoc($esIndexName, $insertDataArray, $permissionObject->id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            if($isTransation){
               DB::rollBack();
            }

            plog(['error' => 'EsAddPermissionJobError','$esResult' => $esResult,'$permissionObject' => $permissionObject,'$adminObject' => $adminObject], 'AddMenuListener', 'handleError');
            throw new CommonException('EsAddPermissionError');
            
        }
    }
}
