<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:41:30
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperUserRoleListener
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

        $data = [];
        $date = date('Y-m-d H:i:s',time());
        $time = time();
        $user_id = $user->id;

        //默认给开发者基础角色的所有身份
        $data[] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user->id,'role_id'=>1];
        $data[] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user->id,'role_id'=>2];
        $data[] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user->id,'role_id'=>3];
        $data[] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user->id,'role_id'=>4];

        $result = UserRoleUnion::insert($data);

        if(!$result)
        {
            throw new CommonException('AddDeveloperRoleError');
        }
    }
}
