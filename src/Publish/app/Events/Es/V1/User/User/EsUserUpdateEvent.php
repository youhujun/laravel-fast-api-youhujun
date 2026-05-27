<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-23 19:11:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-23 19:21:36
 * @FilePath: \youhu-laravel-api-12\app\Events\Es\V1\User\User\EsUserUpdateEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\Es\V1\User\User;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @see \App\Listeners\Es\V1\User\User\EsUserUpdateEvent\EsUserUpdateListener
 */
class EsUserUpdateEvent 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $userObject;
    //兼容手机端
    public ?Admin $adminObject;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $userObject,Admin $adminObject,bool $isTransation = false)
    {
        //
        $this->userObject = $userObject;
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
