<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 21:11:00
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\Publish\app\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\Login;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\AdminLogoutLogLitener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\ClearAdminCacheListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\EsSyncAdminListener
 */
class AdminLogoutEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $adminObject;
    public $token;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($adminObject, $token)
    {
        $this->admin = $adminObject;
        $this->token = $token;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
