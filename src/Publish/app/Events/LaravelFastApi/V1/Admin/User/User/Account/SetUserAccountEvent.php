<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-11 14:12:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-11 14:41:05
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\User\Account;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountDTO;
/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountEvent\SetUserAccountListener
 */
class SetUserAccountEvent 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Admin $adminObject;
    public SetUserAccountDTO $requestDTO;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $adminObject,SetUserAccountDTO $requestDTO,bool $isTransation = false)
    {
        //
        $this->adminObject = $adminObject;
        $this->requestDTO = $requestDTO;
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
