<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 11:11:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 11:36:39
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\System\Permission\MoveMenuEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\System\Permission;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\System\Permission\Permission;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\System\Permission\MoveMenuEvent\MoveMenuListener
 */
class MoveMenuEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Permission $permissionObject;
    public Admin $adminObject;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Permission $permissionObject, Admin $adminObject,bool $isTransation = false)
    {
        $this->permissionObject = $permissionObject;
        $this->adminObject = $adminObject;
        $this->isTransation = $isTransation;
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
