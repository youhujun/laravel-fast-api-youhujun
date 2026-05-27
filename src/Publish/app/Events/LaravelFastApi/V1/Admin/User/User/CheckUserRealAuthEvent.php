<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-26 07:36:08
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-13 21:18:18
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent.php
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\RealAuthUserDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent\UpdateUserRealAuthApplyListener
 */
class CheckUserRealAuthEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Admin $adminObject;
    public RealAuthUserDTO $requestDTO;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $adminObject, RealAuthUserDTO $requestDTO)
    {
        $this->adminObject = $adminObject;
        $this->requestDTO = $requestDTO;
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
