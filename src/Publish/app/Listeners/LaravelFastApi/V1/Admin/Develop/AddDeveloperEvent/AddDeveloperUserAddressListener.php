<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:39:11
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserAddressListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
/**
 * @see \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperUserAddressListener
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
        $user = $event->user;
        $validated = $event->validated;

         //用户地址
        $userAddress = new UserAddress;

        $userAddress->user_id = $user->id;
        $userAddress->created_at = time();
        $userAddress->created_time = time();
        $userAddress->is_default = 1;
        $userAddress->address_type = 10;

        $result =  $userAddress->save();

        if(!$result)
        {
            throw new CommonException('AddDeveloperAddressError');
        }
    }
}
