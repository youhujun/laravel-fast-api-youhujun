<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-03 10:42:32
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-11 02:01:48
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAmountListener.php
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
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserAmountListener
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
        $adminObject = $event->adminObject;

        $userAmountObject = ShardHelperFacade::createWithShard(UserAmount::class, $userObject->biz_id, [
            'user_uid' => $userObject->biz_id,
            'sort' => 100,
        ]);

        if (!isset($userAmountObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserAmountError');
        }

        $indexName = config('common_es.indices.user.user_amounts');

        $configKey = get_shard_config_key();

        $insertDataArray = [
            '_docId' => $userAmountObject->user_amount_uid,
            'shard_key' => $userAmountObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userAmountObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userAmountObject->user_uid, 'user_amounts', $configKey),
            'user_uid' => $userAmountObject->user_uid,
            'amount' => $userAmountObject->amount,
            'bonus' => $userAmountObject->bonus,
            'prepare_bonus' => $userAmountObject->prepare_bonus,
            'coin' => $userAmountObject->coin,
            'score' => $userAmountObject->score,
            'note' => $userAmountObject->note,
            'sort' => $userAmountObject->sort,
            'created_time' => $userAmountObject->created_time,
            'updated_time' => $userAmountObject->updated_time,
            'created_at' => $userAmountObject->created_at,
            'updated_at' => $userAmountObject->updated_at,
            'deleted_at' => $userAmountObject->deleted_at

        ];

        $esResult = EsFacade::createDoc($indexName, $insertDataArray, $userAmountObject->biz_id);

        if (!isset($esResult) || !isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加用户账户数据失败','$esResult' => $esResult,'$userAmountObject' => $userAmountObject,'$adminObject' => $adminObject], 'AddUserAmountListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsAddUserAmountError');
        }
    }
}
