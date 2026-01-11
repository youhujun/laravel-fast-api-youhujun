<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:39:35
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-30 11:26:47
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserRoleListener
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
        $isTransation = $event->isTransation;

        $data = [];
        $date = date('Y-m-d H:i:s',time());
        $time = time();
        $user_id = $user->id;

        $data[0] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user_id,'role_id'=>40];

        $userRoleUnionResult = UserRoleUnion::insert($data);

        if(!$userRoleUnionResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }
            throw new CommonException('AddUserRoleError');
        }
    }
}
