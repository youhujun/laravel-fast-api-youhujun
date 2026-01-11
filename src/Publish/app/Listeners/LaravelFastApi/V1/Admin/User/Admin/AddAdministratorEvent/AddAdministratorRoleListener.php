<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-04 22:44:02
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent\AddAdministratorRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;


/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\Admin\AddAdministratorEvent
 */
class AddAdministratorRoleListener
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
        $addAdmin = $event->addAdmin;
        $validated = $event->validated;

         //定义角色容器
        $roleArray = [];

        //定义数据容器
        $data = [];

        foreach ($validated['role'] as $key => $value)
        {
            $roleArray[] = $value[0];
        }

        //没有管理员角色
        if (!in_array(10, $roleArray) && !in_array(20, $roleArray) && !in_array(30, $roleArray))
        {
            throw new CommonException('SelectNoAdminRoleError');
        }

        foreach ($roleArray as $key => $value)
        {
            $data[] = ['created_at' => date('Y-m-d H:i:s',time()), 'created_time' => time(), 'user_id' => $validated['user_id'], 'role_id' => $value];
        }

        $userRoleUnionResult = UserRoleUnion::insert($data);

        if (!$userRoleUnionResult)
        {
            throw new CommonException('AddAdminRoleError');
        }

    }
}
