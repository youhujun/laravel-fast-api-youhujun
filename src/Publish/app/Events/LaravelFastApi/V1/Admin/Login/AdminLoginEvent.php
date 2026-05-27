<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:51:33
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 00:22:05
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent.php
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
use App\DTOs\LaravelFastApi\V1\Admin\Login\AdminLoginDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/** 用户登录日志
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\AdminLoginLogListener
 * 存储管理员角色
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\CacheAdminRolesListener
 */
class AdminLoginEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Admin $adminObject;
    public AdminLoginDTO $loginDTO;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $adminObject, AdminLoginDTO $loginDTO)
    {
        //
        $this->adminObject = $adminObject;
        $this->loginDTO = $loginDTO;
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
