<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-26 07:36:08
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-26 07:39:28
 * @FilePath: \app\Events\Admin\User\User\CheckUserRealAuthEvent.php
 */

namespace App\Events\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent\UpdateUserRealAuthApplyListener
 */
class CheckUserRealAuthEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $validated;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($admin,$validated)
    {

        $this->admin = $admin;
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
