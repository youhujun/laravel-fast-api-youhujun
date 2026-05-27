<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:08:01
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\User\Info\UserCascader;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserRoleListener
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
        $userObject = $event->userObject;
        $isTransation = $event->isTransation;

        $userRoleUnionObject = ShardHelperFacade::createWithShard(UserRoleUnion::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'role_id' => 40,
            'type' => 20
        ]);

        if (!isset($userRoleUnionObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }
            throw new CommonException('AddUserRoleError');
        }

        $indexName = config('common_es.indices.union.user_role_unions');

        $configKey = get_shard_config_key();

        $insertDataArray = [
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

        $result = EsFacade::createDoc($indexName, $insertDataArray, $userRoleUnionObject->biz_id);

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es添加用户角色失败','$result' => $result,'$insertDataArray' => $insertDataArray], 'AddUserRoleListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsAddUserRoleError');
        }

        $userCascaderObject = ShardHelperFacade::createWithShard(UserCascader::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'role_cascader_json' => json_encode([[40]]),
            
        ]);

        if (!isset($userCascaderObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }
            throw new CommonException('AddUserCascaderRoleError');
        }
    }
}
