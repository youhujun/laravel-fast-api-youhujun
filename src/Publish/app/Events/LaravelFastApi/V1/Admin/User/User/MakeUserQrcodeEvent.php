<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-01 15:45:54
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-01 15:49:47
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener
 */
class MakeUserQrcodeEvent 
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
    public function __construct($user,$admin,$validated,$isTransation = 0)
    {
        //
        $this->user = $user;
        $this->admin = $admin;
        $this->validated = $validated;
        $this->isTransation = $isTransation;
    }

	
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');

		// return new Channel('channel-name');
    }
}
