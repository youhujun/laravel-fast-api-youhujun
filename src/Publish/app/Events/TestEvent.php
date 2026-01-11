<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 16:20:31
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-03-05 15:49:42
 * @FilePath: \app\Events\TestEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $admin;
    public $validated;
    // 是否开启事务
    public $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($admin)
    {
		$this->admin = $admin;
    }

	/**
     * 广播事件名称
     *
     * @return string
     */
     public function broadcastAs()
    {
        return 'AdminTest';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('admin_test');

		return new Channel('admin_test');
    }
}
