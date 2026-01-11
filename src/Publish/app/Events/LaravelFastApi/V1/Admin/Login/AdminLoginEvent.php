<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:51:33
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 16:29:26
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent.php
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

use App\Models\LaravelFastApi\V1\Admin\Admin;

/** 用户登录日志
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\AdminLoginLogListener
 * 存储管理员角色
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\CacheAdminRolesListener
 */
class AdminLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $validated;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $admin,$validated)
    {
        //
        $this->admin = $admin;
        $this->validated = $validated;
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
