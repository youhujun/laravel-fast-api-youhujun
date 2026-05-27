<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-07 20:08:14
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 15:13:20
 * @FilePath: \youhu-laravel-api-12\app\Events\Admin\CommonEvent.php
 */

namespace App\Events\Admin;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Listeners\Admin\CommonEvent\CommonEventListener
 */
class CommonEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Admin $adminObject;
    public mixed $logData;
    public string $eventCode;

    //是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $adminObject, mixed $logData, string $eventCode, bool $isTransation = false)
    {
        $this->adminObject = $adminObject;
        $this->logData = $logData;
        $this->eventCode = $eventCode;
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
