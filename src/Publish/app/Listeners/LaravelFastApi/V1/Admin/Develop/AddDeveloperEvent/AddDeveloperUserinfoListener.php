<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:40:10
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserinfoListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
/**
 * @see \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperUserinfoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        $admin = $event->admin;
        $validated = $event->validated;

        //用户详情
        $userInfo = new UserInfo;

        $userInfo->created_at = time();
        $userInfo->created_time = time();

        $userInfo->user_id = $user->id;
        $userInfo->nick_name = $validated['username'];

        $result = $userInfo ->save();

        if(!$result)
        {
            throw new CommonException('AddDeveloperInfoError');
        }
    }
}
