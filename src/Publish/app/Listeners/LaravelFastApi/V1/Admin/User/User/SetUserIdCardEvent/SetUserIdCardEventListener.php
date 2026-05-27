<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-14 11:09:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-13 04:38:03
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent\SetUserIdCardEventListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\SetUserIdCardDTO;

/**
 *@see \App\Events\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent
 */
class SetUserIdCardEventListener
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
        $requestDTO = $event->requestDTO;
        $adminObject = $event->adminObject;
        $userInfoObject = $event->userInfoObject;

        //用户身份证
        $indexNname = config('common_es.indices.user.users');

        $updateDataArray = [
            'id_number' => $userInfoObject->id_number,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexNname, $userInfoObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新用户身份证号失败','esResult' => $esResult,'$userInfoObject' => $userInfoObject,'adminObject' => $adminObject], 'SetUserIdCardEventListener', 'handleError');

            throw new CommonException('EsUpdateUserIdNumberError');
        }

        //用户状态

        //先更新mysql用户状态
        $userObject = User::queryByShard($requestDTO->user_uid)->where('user_uid', $requestDTO->user_uid)->first();

        if (!isset($userObject->biz_id)) {
            plog(['error' => '用户不存在!','$userObject' => $userObject,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'SetUserIdCardEventListener', 'handleError');
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            //改为40已通过
            'real_auth_status' => 40,
        ];

        $updateResult = $userObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            plog(['error' => '用户修改认证状态失败!','$userObject' => $userObject,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject], 'SetUserIdCardEventListener', 'handleError');
            throw new CommonException('UpdateUserRealAuthStatusError');
        }

        $indexName = config('common_es.indices.user.users');

        $esUpdateDataArray = [
            'real_auth_status' => 40,
            'update_at' => date('Y-m-d H:i:s'),
            'update_time' => time()
        ];

        $esResult = EsFacade::updateDoc($indexName, $requestDTO->user_uid, $esUpdateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es用户修改认证状态失败!','$userObject' => $userObject,'$requestDTO' => $requestDTO,'$adminObject' => $adminObject,'$esResult' => $esResult], 'SetUserIdCardEventListener', 'handleError');
            
            throw new CommonException('UpdateUserRealAuthStatusError');
        }

        //先添加mysql日志
        $insertDataArray = [
            'user_uid' => $requestDTO->user_uid,
            //审核管理员uid
            'admin_uid' => $adminObject->biz_id,
            'data_type' => 1,
            //申请状态自动通过
            'status' => 20,
            'auth_apply_at' => date('Y-m-d H:i:s'),
            'auth_apply_time' => time(),
            'auth_at' => date('Y-m-d H:i:s'),
            'auth_time' => time(),
            'refuse_info' => '',
            'sort' => 100
        ];

        $userRealAuthLogObject = ShardHelperFacade::createWithShard(UserRealAuthLog::class, $requestDTO->user_uid, $insertDataArray);

        if (!isset($userRealAuthLogObject->biz_id)) {
            plog(['error' => '添加用户实名认证日志失败','$userRealAuthLogObject' => $userRealAuthLogObject,'$requestDTO' => $requestDTO,'adminObject' => $adminObject], 'SetUserIdCardEventListener', 'handleError');
            throw new CommonException('AddUserRealAuthLogError');
        }

        //添加es日志

        $indexName = config('common_es.indices.logs.user_real_auth_logs');

        $configKey = get_shard_config_key();

        $esInsertDataArray = [
            '_docId' => $userRealAuthLogObject->biz_id,
            'user_real_auth_log_uid' => $userRealAuthLogObject->biz_id,
            'user_uid' => $userRealAuthLogObject->user_uid,
            'shard_key' => $userRealAuthLogObject->shard_key,
            'shard_db' => ShardFacade::getDbName($userRealAuthLogObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($userRealAuthLogObject->user_uid, 'user_real_auth_logs', $configKey),
            //审核管理员uid
            'admin_uid' => $userRealAuthLogObject->admin_uid,
            'data_type' => $userRealAuthLogObject->data_type,
            //申请状态自动通过
            'status' => $userRealAuthLogObject->status,
            'auth_apply_at' => $userRealAuthLogObject->auth_apply_at,
            'auth_apply_time' => $userRealAuthLogObject->auth_apply_time,
            'auth_at' => $userRealAuthLogObject->auth_at,
            'auth_time' => $userRealAuthLogObject->auth_time,
            'refuse_info' => $userRealAuthLogObject->refuse_info,
            'sort' => $userRealAuthLogObject->sort,
            'created_at' => $userRealAuthLogObject->created_at,
            'created_time' => $userRealAuthLogObject->created_time,
            'updated_at' => $userRealAuthLogObject->updated_at,
            'updated_time' => $userRealAuthLogObject->updated_time,
            'deleted_at' => $userRealAuthLogObject->deleted_at
        ];

        $esResult = EsFacade::createDoc($indexName, $esInsertDataArray, $userRealAuthLogObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es用户实名认证日志失败','esResult' => $esResult,'$userRealAuthLogObject' => $userRealAuthLogObject,'adminObject' => $adminObject], 'EsAddUserRealAuthLogJob', 'handleError');
            throw new CommonException('EsAddUserRealAuthLogError');
        }
    }
}
