<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 12:00:41
 * @FilePath: \app\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Events\LaravelFastApi\V1\Phone\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserInfoListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAlbumListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAvatarListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserRoleListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAmountListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserQrcodeListener
 * @see \App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserSourceListener
 */
class UserRegisterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
	//传递过来的数组数据
    public $validated;
    // 是否开启事务
    public $isTransation;
	// 是否启用用户二位码
	public $isOpenQrcode;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user,$validated,$isTransation = 0,$isOpenQrcode = 1)
    {
        //
        $this->user = $user;
        $this->validated = $validated;
        $this->isTransation = $isTransation;
		$this->isOpenQrcode = $isOpenQrcode;
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
