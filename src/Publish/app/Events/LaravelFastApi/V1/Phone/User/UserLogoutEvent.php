<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 01:31:35
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Phone\User\UserLogoutEvent.php
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
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent\AddPhoneUserLogListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent\ClearPhoneUserCacheListener
 */
class UserLogoutEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public User $userObject;
    public string $token;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $userObject, string $token, bool $isTransation = false)
    {
        //
        $this->userObject = $userObject;
        $this->token = $token;
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
