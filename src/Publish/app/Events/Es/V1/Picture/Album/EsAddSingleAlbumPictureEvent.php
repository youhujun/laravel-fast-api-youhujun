<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-24 00:45:14
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 02:02:16
 * @FilePath: \youhu-laravel-api-12\app\Events\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Events\Es\V1\Picture\Album;

use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\User\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

/**
 * @see \App\Listeners\Es\V1\Picture\Album\EsAddSingleAlbumPictureEvent\EsAddSingleAlbumPictureListener
 */
class EsAddSingleAlbumPictureEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AlbumPicture $albumPictureObject;
    public ?User $userObject;
    public ?Admin $adminObject;
    // 是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AlbumPicture $albumPictureObject,User $userObject = null,Admin $adminObject = null,bool $isTransation = false)
    {
        $this->albumPictureObject = $albumPictureObject;
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
