<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-04 20:04:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-16 08:57:41
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent.php
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorAlbumListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorRoleListener
 */
class AddAdministratorEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $addAdmin;
    public $validated;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($admin,$addAdmin,$validated)
    {
        //
        $this->admin = $admin;
        $this->addAdmin = $addAdmin;
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
