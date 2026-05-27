<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-26 08:29:50
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\ClearAdminCacheListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent
 */
class ClearAdminCacheListener
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
        $token = $event->token;

        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminInfoKey = config('common_redis.admin_info.key');
        $redisAdminRolesKey = config('common_redis.admin_roles.key');

        $redisAdminField = config('common_redis.admin.field');
        $redisAdminInfoField = config('common_redis.admin_info.field');
        $redisAdminRolesField = config('common_redis.admin_roles.field');

        Redis::del($redisAdminTokenKey.$token);
        Redis::hdel($redisAdminKey, $redisAdminField.$adminObject->biz_id);
        Redis::hdel($redisAdminInfoKey, $redisAdminInfoField.$adminObject->biz_id);
        Redis::hdel($redisAdminRolesKey, $redisAdminRolesField.$adminObject->biz_id);
    }
}
