<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-29 16:14:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 17:05:24
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Common\V1\User\User\CommonUserCertificateEvent\CommonUserCertificateListener.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserCertificateEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\UserCertification;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Jobs\LaravelFastApi\V1\Phone\User\User\AfterLogin\EsAddUserCeetificationJob;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserCertificateEvent
 */
class CommonUserCertificateListener
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
        $certificateArray = $event->certificateArray;
        $isTransation =  $event->isTransation;

        $userCertificationObject = ShardHelperFacade::createWithShard(UserCertification::class, $userObject->biz_id, $certificateArray);

        if (!isset($userCertificationObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserCertificationError');
        }

        EsAddUserCeetificationJob::dispatch($userObject, $userCertificationObject)->delay(now()->addSeconds(4));
    }
}
