<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:54:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-14 23:14:44
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\CacheAdminRolesListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Exceptions\Admin\CommonException;

use Illuminate\Support\Facades\Redis;


/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent
 * @see \App\Models\LaravelFastApi\V1\Admin\Admin
 */
class CacheAdminRolesListener
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
        $admin = $event->admin;
        $validated= $event->validated;

        $rolesArray = getAdminRoles($admin);

        $error = 0;

        $hasResult = Redis::hget("admin_roles:admin_roles",$admin->id);

        if($hasResult)
        {
            $error = 1;
        }
        else
        {
            if(count($rolesArray))
            {
                $error =  Redis::hset("admin_roles:admin_roles",$admin->id,json_encode($rolesArray));
            }
        }

        if(!$error)
        {
            throw new Commonexception('CacheAdminRolesError');
        }

    }
}
