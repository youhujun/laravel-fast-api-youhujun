<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 16:33:20
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent.php
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
 */
class AdminLogoutEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $token;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($admin,$token)
    {
        $this->admin = $admin;
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
