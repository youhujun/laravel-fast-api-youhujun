<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:44:22
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\ClearAdminCacheListener.php
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
        $admin = $event->admin;
        $token = $event->token;

        Redis::del("admin_token:{$token}");
        Redis::hdel("admin:admin",$admin->id);
        Redis::hdel("admin_info:admin_info",$admin->id);
        Redis::hdel("admin_roles:admin_roles",$admin->id);
    }
}
