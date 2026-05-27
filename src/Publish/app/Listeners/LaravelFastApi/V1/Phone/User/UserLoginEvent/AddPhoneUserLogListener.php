<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 15:59:34
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-27 23:55:53
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent\AddPhoneUserLogListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Phone\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;
use App\Jobs\LaravelFastApi\V1\Phone\User\User\Log\EsAddUserLoginLogJob;

/**
 * @see \App\Events\Phone\User\UserLoginEvent
 */
class AddPhoneUserLogListener
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

        $insertDataArray = [
            'user_login_log_uid' => get_snow_flake_id(),
            'user_uid' => $userObject->biz_id,
            'data_type' => 1,
            'login_type' => 20,
            'status' => 10,
            'ip' => Request::getClientIp(),
            'instruction' => '用户登录'
        ];

        $userLoginLogObject = ShardHelperFacade::createWithShard(UserLoginLog::class, $userObject->biz_id, $insertDataArray);

        if (!isset($userLoginLogObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddPhoneUserLoginLogError');
        }

        EsAddUserLoginLogJob::dispatch($userLoginLogObject, $userObject)->delay(now()->addseconds(3));
    }
}
