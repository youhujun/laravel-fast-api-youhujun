<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-11 09:22:52
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Events\LaravelFastApi\V1\Admin\Develop;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminAlbumListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAddressListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAvatarListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserinfoListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserQrcodeListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserRoleListener
 */
class AddDeveloperEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $user;
    public $validated;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $admin,User $user,$validated)
    {
        $this->admin = $admin;
        $this->user = $user;
        $this->validated = $validated;
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
