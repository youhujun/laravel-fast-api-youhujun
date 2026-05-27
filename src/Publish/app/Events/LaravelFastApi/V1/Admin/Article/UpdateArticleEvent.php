<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-24 03:17:26
 * @FilePath: \youhu-laravel-api-12\app\Events\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Events\LaravelFastApi\V1\Admin\Article;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Article\Article;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\UpdateArticleDTO;

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleInfoListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleCategoryUnionListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleLabelUnionListener
 */
class UpdateArticleEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Admin $adminObject;
    public Article $articleObject;
    public UpdateArticleDTO $requestDTO;
    //是否开启事务
    public bool $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Admin $adminObject, Article $articleObject, UpdateArticleDTO $requestDTO, bool $isTransation = true)
    {
        $this->adminObject = $adminObject;
        $this->articleObject = $articleObject;
        $this->requestDTO = $requestDTO;
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
