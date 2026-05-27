<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:36:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 17:37:32
 * @FilePath: \youhu-laravel-api-12\app\Events\Common\V1\User\User\CommonUserRegisterEvent.php
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
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\DTOs\Common\V1\User\User\BusinessRegisterUserDTO;
/**
*  @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserInfoListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAlbumListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAvatarListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserRoleListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAmountListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserQrcodeListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserSourceListener
 * @see \App\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddApiUserAuthListener
 *
 */
class CommonUserRegisterEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public BusinessRegisterUserDTO $businessRegisterUserDTO;
    public User $userObject;
    public bool $isTransation;
    //兼容手机端
    public ?Admin $adminObject = null;
    // 是否开启事务

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BusinessRegisterUserDTO $businessRegisterUserDTO, User $userObject, bool $isTransation = false,?Admin $adminObject = null)
    {
        $this->businessRegisterUserDTO = $businessRegisterUserDTO;
        $this->userObject = $userObject;
        $this->isTransation = $isTransation;
        $this->adminObject = $adminObject ?? null;
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
