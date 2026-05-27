<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 08:30:23
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\AdminLogoutLogLitener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;
use App\Jobs\LaravelFastApi\V1\Admin\Login\EsAddAdminLogoutLogJob;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent
 */
class AdminLogoutLogLitener
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

        $ip = Request::getClientIp();

        $admin_uid = $adminObject->biz_id;

        $adminLoginLogObject = ShardHelperFacade::createWithShard(
            AdminLoginLog::class,
            $admin_uid,
            [
                'admin_login_log_uid' => get_snow_flake_id(),
                'admin_uid' => $admin_uid,
                'status' => 20,
                'instruction' => '管理员退出',
                'ip' => $ip,
                'data_type' => 1,
                'login_type' => 10
            ]
        );

        if (!isset($adminLoginLogObject->biz_id)) {
            throw new CommonException('AdminLogoutLogError');
        }

        EsAddAdminLogoutLogJob::dispatch($adminLoginLogObject, $adminObject)->delay(now()->addSeconds(5));
    }
}
