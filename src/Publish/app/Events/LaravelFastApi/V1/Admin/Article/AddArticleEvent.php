<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-16 12:03:12
 * @FilePath: \app\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent.php
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

/**
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleInfoListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleCategoryUnionListener
 * @see \App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleLabelUnionListener
 */
class AddArticleEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $admin;
    public $article;
    public $validated;
    //是否开启事务
    public $isTransation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($admin,$article,$validated,$isTransation = 0)
    {
        $this->admin = $admin;
        $this->article = $article;
        $this->validated = $validated;
        $this->isTransation = $isTransation;
    }

    /**
     * 广播事件名称
     *
     * @return string
     */
     public function broadcastAs()
    {
        return 'add.article';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
        return new Channel('article');
    }
}
