<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-12 20:07:14
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent\UpdateUserRealAuthApplyListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\RealAuthUserDTO;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\RealAuth\EsCheckUserRealAuthStatusJob;
use App\Jobs\LaravelFastApi\V1\Admin\User\User\RealAuth\EsCheckUserRealAuthLogStatusJob;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent
 */
class UpdateUserRealAuthApplyListener
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
        $adminObject = $event->adminObject;
        $requestDTO = $event->requestDTO;

        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            //默认认证不通过
            'real_auth_status' => 30,
            'update_at' => date('Y-m-d H:i:s'),
            'update_time' => time()
        ];

        if ($requestDTO->is_real_auth) {
            $updateDataArray['real_auth_status'] = 40;
        }

        $esResult = EsFacade::updateDoc($indexName, $requestDTO->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es用户修改认证状态失败!','$requestDTO' => $requestDTO,'$adminObject' => $adminObject,'$esResult' => $esResult], 'UpdateUserRealAuthApplyListener', 'handleError');

            throw new CommonException('EsRealAuthUserError');
        }
        
        
        $indexName = config('common_es.indices.logs.user_real_auth_logs');

        $updateDataArray = [
            //默认认证不通过
            'status' => 30,
            'update_at' => date('Y-m-d H:i:s'),
            'update_time' => time(),
            'auth_at' => date('Y-m-d H:i:s'),
            'auth_time' => time(),
            'refuse_info' => $requestDTO->refuse_info
        ];

        if ($requestDTO->is_real_auth) {
            $updateDataArray['status'] = 20;
        }

        $esResult = EsFacade::updateDoc($indexName, $requestDTO->user_real_auth_log_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es修改认日志证状态失败!','$requestDTO' => $requestDTO,'$adminObject' => $adminObject,'$esResult' => $esResult], 'UpdateUserRealAuthApplyListener', 'handleError');

            throw new CommonException('EsRealAuthUserLogError');
        }
    }
}
