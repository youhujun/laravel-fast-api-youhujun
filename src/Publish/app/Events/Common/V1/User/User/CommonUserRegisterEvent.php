<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:36:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 12:01:19
 * @FilePath: \app\Events\Common\V1\User\User\CommonUserRegisterEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events\Common\V1\User\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\LaravelFastApi\V1\User\User;
use  App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserInfoListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAlbumListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAvatarListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserRoleListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAmountListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserSourceListener
 */
class CommonUserRegisterEvent 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $admin;
    public $validated;
    // 是否开启事务
    public $isTransation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($param = [])
    {
        foreach ($param as $key => $item) 
		{
			$this->{$key} = $item;
		}
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
