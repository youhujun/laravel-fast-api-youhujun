<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer & codebuddy
 * @Date: 2026-03-01 01:04:31
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-11 02:36:06
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddApiUserAuthListener.php
 * Copyright (C) 2026 youhujun & xueer & codebuddy. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;
use App\Models\LaravelFastApi\V1\Api\Auth\YouHuAuthService;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddApiUserAuthListener
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

        $user_uid = $userObject->biz_id;

        $encryptedSecretKey = '';

        $encryptedAuthToken = '';

        $serviceFlag = 'youhu-base';

        // UUID（char(36)）
        $authToken = Str::uuid()->toString();

        $secretKey = KeyManagerFacade::generateSecureSecretKey(40, ['letters_upper', 'letters_lower', 'numbers']);

        $aesKey = config('common.aes.key');

        $encryptedSecretKey = AESFacade::encrypt($secretKey, $aesKey);

        $encryptedAuthToken = AESFacade::encrypt($authToken, $aesKey);

        //p('验证通过');

        $authDataArray = [
            'user_uid' => $user_uid,
            'secret_key' => $encryptedSecretKey,
            'auth_token' => $encryptedAuthToken,
            'service_flag' => $serviceFlag,
            'status' => 1
        ];

        $youhuAuthServiceObject = ShardHelperFacade::createWithShard(YouHuAuthService::class, $user_uid, $authDataArray);

        if (!isset($youhuAuthServiceObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddYouHuAuthServiceError');
        }

        $indexName = config('common_es.indices.user.youhu_auth_services');

        $configKey = get_shard_config_key();

        $dataArray = [
            'user_uid' => $userObject->biz_id,
            'sahrd_key' => $youhuAuthServiceObject->shard_key,
            'service_flag' => $youhuAuthServiceObject->service_flag,
            'secret_key' => $youhuAuthServiceObject->encrypted_secret_key,
            'auth_token' => $youhuAuthServiceObject->encrypted_auth_token,
            'status' => 1,
            'created_at' => $youhuAuthServiceObject->created_at,
            'updated_at' => $youhuAuthServiceObject->updated_at,
            'shard_db' =>  ShardFacade::getDbName($userObject->biz_id, $configKey),
            'shard_table' => ShardFacade::getTableName($userObject->user_uid, 'youhu_auth_services', $configKey),
        ];

        $result = EsFacade::createDoc($indexName, $dataArray, $userObject->biz_id);

        if (!isset($result['code']) || $result['code'] != 0) {
            plog(['error' => 'es同步用户认证服务数据失败','$result' => $result], 'AddApiUserAuthListener', 'handleError');

            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('EsAddYouHuAuthServiceError');
        }
    }
}
