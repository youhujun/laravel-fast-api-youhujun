<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-04 20:45:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 01:07:57
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\User\Admin\UpdateAdministratorEvent.php
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\UpdateAdminDTO;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\Admin\UpdateAdministratorEvent\UpdateAdministratorRoleListener
 */
class UpdateAdministratorEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Admin $loginAdminObject;
    public Admin $adminObject;
    public UpdateAdminDTO $requestDTO;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $loginAdminObject, Admin $adminObject, $requestDTO)
    {
        $this->loginAdminObject = $loginAdminObject;
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
