<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-14 11:06:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-14 11:07:33
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\User;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\SetUserIdCardDTO;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent\SetUserIdCardEventListener
 */
class SetUserIdCardEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public SetUserIdCardDTO $requestDTO;
    public UserInfo $userInfoObject;
    public Admin $adminObject;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SetUserIdCardDTO $requestDTO, UserInfo $userInfoObject, Admin $adminObject)
    {
        //
        $this->requestDTO = $requestDTO;
        $this->userInfoObject = $userInfoObject;
        $this->adminObject = $adminObject;
    }

    /**
     * 广播事件名称
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'replaceEventName';
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
