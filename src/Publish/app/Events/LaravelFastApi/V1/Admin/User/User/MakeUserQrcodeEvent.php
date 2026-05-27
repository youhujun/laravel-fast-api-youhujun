<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-01 15:45:54
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-12 16:55:00
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent.php
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
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\MakeUserQrcodeDTO;

/**
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener
 */
class MakeUserQrcodeEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public MakeUserQrcodeDTO $requestDTO;
    public User $userObject;
    public Admin $adminObject;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MakeUserQrcodeDTO $requestDTO, User $userObject,Admin $adminObject, bool $isTransation = false)
    {
        //
        $this->requestDTO = $requestDTO;
        $this->adminObject = $adminObject;
        $this->userObject = $userObject;
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
