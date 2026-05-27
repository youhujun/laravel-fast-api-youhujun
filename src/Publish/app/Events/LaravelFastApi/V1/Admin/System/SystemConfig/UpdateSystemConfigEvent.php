<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-17 10:55:34
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-17 18:09:47
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\System\SystemConfig\UpdateSystemConfigEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\System\SystemConfig;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\System\SystemConfig;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\System\SystemConfig\UpdateSystemConfigEvent\UpdateSystemConfigListener
 */
class UpdateSystemConfigEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public SystemConfig $systemConfigObject;
    public Admin $adminObject;
    public array $validated;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SystemConfig $systemConfigObject, Admin $adminObject)
    {
        //
        $this->systemConfigObject = $systemConfigObject;
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
