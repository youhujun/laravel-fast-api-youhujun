<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:40:16
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:39:40
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserSourceListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
use App\Models\LaravelFastApi\V1\User\User;
use App\Facades\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserSourceListener
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
        $businessRegisterUserDTO = $event->businessRegisterUserDTO;
        $adminObject = $event->adminObject;

        // p($queryArray);
        // die;

        //是否有上级
        $is_has_superior = false;
        //查找上级用户的id
        $indexName = config('common_es.indices.user.users');

        $esQuery = EsQueryFacade::index($indexName)->whereNull('deleted_at');
        //邀请的id
        if (isset($businessRegisterUserDTO->invite_id) && $businessRegisterUserDTO->invite_id) {
            $is_has_superior = true;
            $esQuery->where('user_uid', $businessRegisterUserDTO->invite_id);
        }

        //邀请码
        if (isset($businessRegisterUserDTO->invite_code) && $businessRegisterUserDTO->invite_code) {
            $is_has_superior = true;
            $esQuery->where('invite_code', $businessRegisterUserDTO->invite_code);
        }

        //后台添加用户
        if (isset($businessRegisterUserDTO->source_user_uid) && $businessRegisterUserDTO->source_user_uid) {
            $is_has_superior = true;
            $esQuery->where('user_uid', $businessRegisterUserDTO->source_user_uid);
        }

        //如果有上级用户
        if ($is_has_superior) {
            $esUserObject = $esQuery->get()->first();

            if (!isset($esUserObject->user_uid)) {
                if ($isTransation) {
                    DB::rollBack();
                    throw new CommonException('InviteSourceUserNotExistsError');
                }
            }

            $updateDataArray = [
                'source_user_uid' => $esUserObject->user_uid,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_time'=>time()
            ];

            //重新查询用户
            $userObject = User::queryByShard($userObject->user_uid)->where('user_uid', $userObject->user_uid)->first();

            //保存用户上级
            $userUpdateResult = $userObject->updateWithShard($updateDataArray);

            if (!$userUpdateResult) {
                if ($isTransation) {
                    DB::rollBack();
                }

                throw new CommonException('SaveUserSourceError');
            }

            $userSourceUnionData = PhoneUserSourceFacade::getInsertUserSourceUnionData($userObject);

            $userSourceUnionData['user_uid'] = $userObject->biz_id;

            $userSourceUnionObject = ShardHelperFacade::createWithShard(UserSourceUnion::class, $userObject->user_uid, $userSourceUnionData);


            if (!isset($userSourceUnionObject->biz_id)) {
                if ($isTransation) {
                    DB::rollBack();
                }

                throw new CommonException('AddUserSourceUnionError');
            }

            $indexName = config('common_es.indices.union.user_source_unions');

            $configKey = get_shard_config_key();

            $insertDataArray = [
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
                'deleted_at' => $userSourceUnionObject->deleted_at
            ];

            $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userSourceUnionObject->biz_id);

            if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es添加用户关联数据失败','$esResult' => $esResult,'$userSourceUnionObject' => $userSourceUnionObject,'$adminObject' => $adminObject], 'AddUserSourceListener', 'handleError');

                 if ($isTransation) {
                    DB::rollBack();
                }

                throw new CommonException('EsAddUserSourceUnionError');
            }
        }
    }
}
