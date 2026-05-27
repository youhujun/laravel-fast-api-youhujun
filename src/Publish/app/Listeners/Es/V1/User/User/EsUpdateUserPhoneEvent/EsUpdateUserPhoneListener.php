<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-24 02:30:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 03:42:29
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Es\V1\User\User\EsUpdateUserPhoneEvent\EsUpdateUserPhoneListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\Es\V1\User\User\EsUpdateUserPhoneEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;;
use App\Exceptions\Common\CommonException;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\User\User;
/**
 * @see \App\Events\Es\V1\User\User\EsUpdateUserPhoneEvent
 */
class EsUpdateUserPhoneListener
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
        $indexName = config('common_es.indices.user.users');

        $updateDataArray = [
            'phone' => $userObject->phone
        ];

        $esResult = EsFacade::updateDoc($indexName, $userObject->user_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新用户手机号失败','$esResult' => $esResult], 'EsUpdateUserPhoneJob', 'handleError');

            throw new CommonException('EsUpdateUserPhoneError');
        }
    }
}
