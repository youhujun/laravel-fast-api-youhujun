<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-18 16:29:22
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\Admin\Info\AdminCascader;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent
 */
class AddAdministratorRoleListener
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
        $loginAdminObject = $event->loginAdminObject;
        $adminObject = $event->adminObject;
        $requestDTO = $event->requestDTO;

        $role_cascader_id_array = $requestDTO->role_cascader_id_array;

        $role_id_array = get_cascader_array($role_cascader_id_array);

        $roleIndexName = config('common_es.indices.system.roles');

        $esRoleIdArray = EsQueryFacade::index($roleIndexName)->where('type', 10)->limit(100)->get()->pluck('id') ->toArray();

        //取交集
        $intersection = array_intersect($esRoleIdArray, $role_id_array);

        //没有管理员角色
        if (empty($intersection)) {
            throw new CommonException('SelectNoAdminRoleError');
        }

        $userRoleUnionDataArray = [];

        foreach ($role_id_array as $key => $value) {
            $userRoleUnionDataArray[] = [
                'user_role_union_uid' => get_snow_flake_id(),
                'user_uid' =>$requestDTO->user_uid,
                'role_id' => $value,
                'type' => 10,
            ];
        }

        $userRoleUnionResult = ShardHelperFacade::insertBatchWithShard(UserRoleUnion::class, $userRoleUnionDataArray);

        if (!$userRoleUnionResult) {
            throw new CommonException('AddAdminRoleError');
        }

        $indexName = config('common_es.indices.union.user_role_unions');

        $userRoleUnionCollection = UserRoleUnion::queryByShard($adminObject->user_uid)->where('user_uid', $adminObject->user_uid)->where('type', 10)->get();

        $insertDataArray = [];

        $configKey = get_shard_config_key();

        foreach ($userRoleUnionCollection as $userRoleUnionObject) {
            $insertDataArray[] = [
                '_docId' => $userRoleUnionObject->user_role_union_uid,
                'shard_key' => $userRoleUnionObject->shard_key,
                'shard_db' => ShardFacade::getDbName($userRoleUnionObject->user_uid, $configKey),
                'shard_table' => ShardFacade::getTableName($userRoleUnionObject->user_uid, 'user_role_unions', $configKey),
                'user_role_union_uid' => $userRoleUnionObject->user_role_union_uid,
                'user_uid' => $userRoleUnionObject->user_uid,
                'role_id' => $userRoleUnionObject->role_id,
                'type' => $userRoleUnionObject->type,
                'created_time' => $userRoleUnionObject->created_time,
                'updated_time' => $userRoleUnionObject->updated_time,
                'created_at' => $userRoleUnionObject->created_at,
                'updated_at' => $userRoleUnionObject->updated_at,
                'deleted_at' => $userRoleUnionObject->deleted_at,
            ];
        }

        $esResult = EsFacade::batchActDoc($indexName, $insertDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es批量添加管理员角色失败','$esResult' => $esResult,'$insertDataArray' => $insertDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'AddAdministratorRoleListener', 'handleError');
            throw new CommonException('EsAddAdminRoleUnionError');
        }

        //添加admin的cascader
        $insertDataArray = [
            'admin_uid'=>$adminObject->biz_id,
            'role_cascader_json' => json_encode($role_cascader_id_array)
        ];

        $adminCasderObject = ShardHelperFacade::createWithShard(AdminCascader::class, $adminObject->biz_id,$insertDataArray);

        if(!isset($adminCasderObject->biz_id)){
            throw new CommonException('AddAdminRoleCascaderError');
        }

    }
}
