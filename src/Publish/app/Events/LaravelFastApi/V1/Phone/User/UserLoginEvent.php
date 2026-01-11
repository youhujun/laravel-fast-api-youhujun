<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-17 18:58:55
 * @FilePath: \app\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Phone\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent\AddPhoneUserLogListener
 */
class UserLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    // 是否开启事务
    public $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user,$isTransation = 0)
    {
        //
        $this->user = $user;
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
    }
}
