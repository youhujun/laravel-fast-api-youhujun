<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 23:02:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-20 21:59:28
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\Union;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\System\Level\Union\UserLevelItemUnion;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade
 */
class EsSyncUnionFacadeService
{
    use EsFacadeServiceBaseTrait;

    public function test()
    {
        echo "EsSyncUnionFacadeService test";
    }

    /**
     * 同步角色和权限关联数据到ES
     */
    public function syncRolePermissionUnions()
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput("开始批量执行所有role_permission_unions数据同步ES--2", 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.union.role_permission_unions');

        RolePermissionUnion::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $rolePermissionCollection = $chunk;
            //p($paramArray);

            $esDataArray = $rolePermissionCollection->map(function ($rolePermissionObject) {
                return [
                    '_docId' => $rolePermissionObject->id,
                    'id' => $rolePermissionObject->id,
                    'permission_id' => $rolePermissionObject->permission_id,
                    'role_id' => $rolePermissionObject->role_id,
                    'created_time' => $rolePermissionObject->created_time,
                    'updated_time' => $rolePermissionObject->updated_time,
                    'created_at' => $rolePermissionObject->created_at,
                    'updated_at' => $rolePermissionObject->updated_at,
                    'deleted_at' => $rolePermissionObject->deleted_at,
                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => "es批量同步role_permission_unions数据失败",'$result' => $result], 'EsSyncUnionFacadeService', 'syncRolePermissionUnionsError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => "批量同步role_permission_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsSyncUnionFacadeService', 'syncRolePermissionUnions');

        if (app()->runningInConsole()) {
            $this->consoleOutput("批量执行所有role_permission_unions数据同步ES结束--2", 'info');
        }
    }

    /**
     * 同步角色和权限关联数据到ES
     */
    public function syncUserLevelItemUnions()
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput("开始批量执行所有user_level_item_unions数据同步ES--2", 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.union.user_level_item_unions');

        UserLevelItemUnion::select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userLevelItemUnionCollection = $chunk;
            //p($paramArray);

            $esDataArray = $userLevelItemUnionCollection->map(function ($userLevelItemUnionObject) {
                return [
                    '_docId' => $userLevelItemUnionObject->id,
                    'id' => $userLevelItemUnionObject->id,
                    'user_level_id' => $userLevelItemUnionObject->user_level_id,
                    'level_item_id' => $userLevelItemUnionObject->level_item_id,
                    'value_type' => $userLevelItemUnionObject->value_type,
                    'value' => $userLevelItemUnionObject->value,
                    'sort' => $userLevelItemUnionObject->sort,
                    'created_time' => $userLevelItemUnionObject->created_time,
                    'updated_time' => $userLevelItemUnionObject->updated_time,
                    'created_at' => $userLevelItemUnionObject->created_at,
                    'updated_at' => $userLevelItemUnionObject->updated_at,
                    'deleted_at' => $userLevelItemUnionObject->deleted_at,
                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => "es批量同步user_level_item_unions数据失败",'$result' => $result], 'EsSyncUnionFacadeService', 'syncUserLevelItemUnionsError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => "批量同步user_level_item_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsSyncUnionFacadeService', 'syncUserLevelItemUnions');

        if (app()->runningInConsole()) {
            $this->consoleOutput("批量执行所有user_level_item_unions数据同步ES结束--2", 'info');
        }
    }

    /**
     * 同步用户和角色关联
     */
    public function syncUserRoleUnions()
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput("开始批量执行所有user_role_unions数据同步ES--2", 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.union.user_role_unions');

        UserRoleUnion::queryByAllShard()
        ->select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userRoleUnionCollection = $chunk;
            //p($paramArray);

            $esDataArray = $userRoleUnionCollection->map(function ($userRoleUnionObject) {
                $configKey = get_shard_config_key();

                return [
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
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => "es批量同步user_role_unions数据失败",'$result' => $result], 'EsSyncUnionFacadeService', 'syncUserRoleUnionsError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => "批量同步user_role_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsSyncUnionFacadeService', 'syncUserRoleUnions');

        if (app()->runningInConsole()) {
            $this->consoleOutput("批量执行所有user_role_unions数据同步ES结束--2", 'info');
        }
    }

    /**
     * 同步用户来源关联数据到ES
     */
    public function syncUserSourceUnions()
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput("开始批量执行所有user_source_unions数据同步ES--2", 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.union.user_source_unions');

        UserSourceUnion::queryByAllShard()
        ->select(['*'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $userSourceUnionCollection = $chunk;
            //p($paramArray);

            $esDataArray = $userSourceUnionCollection->map(function ($userSourceUnionObject) {
                $configKey = get_shard_config_key();

                return [
                    '_docId' => $userSourceUnionObject->user_source_union_uid,
                    'shard_key' => $userSourceUnionObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($userSourceUnionObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($userSourceUnionObject->user_uid, 'user_source_unions', $configKey),
                    'user_source_union_uid' => $userSourceUnionObject->user_source_union_uid,
                    'user_uid' => $userSourceUnionObject->user_uid,
                    'first_uid' => $userSourceUnionObject->first_uid,
                    'second_uid' => $userSourceUnionObject->second_uid,
                    'created_time' => $userSourceUnionObject->created_time,
                    'updated_time' => $userSourceUnionObject->updated_time,
                    'created_at' => $userSourceUnionObject->created_at,
                    'updated_at' => $userSourceUnionObject->updated_at,
                    'deleted_at' => $userSourceUnionObject->deleted_at,
                ];
            })->toArray();

            //p($esDataArray);

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => "es批量同步user_source_unions数据失败",'$result' => $result], 'EsSyncUnionFacadeService', 'syncUserSourceUnionsError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => "批量同步user_source_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsSyncUnionFacadeService', 'syncUserSourceUnions');

        if (app()->runningInConsole()) {
            $this->consoleOutput("批量执行所有user_source_unions数据同步ES结束--2", 'info');
        }
    }
}
