<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:54:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 01:58:40
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\AdminLoginLogListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;
use App\Jobs\LaravelFastApi\V1\Admin\Login\EsAddAdminLoginLogJob;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent
 */
class AdminLoginLogListener
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
                'status' => 10,
                'instruction' => '管理员登录',
                'ip' => $ip,
                'data_type' => 1,
                'login_type' => 10
            ]
        );

        if (!isset($adminLoginLogObject->biz_id)) {
            throw new CommonException('AdminLoginLogError');
        }

        EsAddAdminLoginLogJob::dispatch($adminLoginLogObject, $adminObject)->delay(now()->addSeconds(5));
    }
}
