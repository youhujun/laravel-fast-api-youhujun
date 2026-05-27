<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:54:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-20 22:04:25
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\CacheAdminRolesListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Admin\CommonException;
use Illuminate\Support\Facades\Redis;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent
 * @see \App\Models\LaravelFastApi\V1\Admin\Admin
 */
class CacheAdminRolesListener
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

        $rolesArray = get_admin_roles($adminObject);

        // p($rolesArray);
        // die;

        $error = 0;

        $redisAdminTokenKey = config('common_redis.admin_token.key');
        $redisAdminKey = config('common_redis.admin.key');
        $redisAdminInfoKey = config('common_redis.admin_info.key');
        $redisAdminRolesKey = config('common_redis.admin_roles.key');

        $redisAdminField = config('common_redis.admin.field');
        $redisAdminInfoField = config('common_redis.admin_info.field');
        $redisAdminRolesField = config('common_redis.admin_roles.field');


        $hasResult = Redis::hget($redisAdminRolesKey, $redisAdminRolesField.$adminObject->biz_id);

        if ($hasResult) {
            $error = 1;
        } else {
            if (count($rolesArray)) {
                $error =  Redis::hset($redisAdminRolesKey, $redisAdminRolesField.$adminObject->biz_id, json_encode($rolesArray));
            }
        }

        if (!$error) {
            throw new CommonException('CacheAdminRolesError');
        }
    }
}
