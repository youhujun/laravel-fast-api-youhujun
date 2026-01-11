<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 13:46:35
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent\AdminLogoutLogLitener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

use App\Exceptions\Admin\CommonException;

/**
 * @see \App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog
 */
use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;

/**
 * @see \App\Events\Admin\Login\AdminLogoutEvent
 */
class AdminLogoutLogLitener
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
        $ip = Request::getClientIp();

        $adminLoginLog = new AdminLoginLog;

        $data = ['admin_id'=>$admin->id,'status'=>20,'instruction'=>'管理员退出','ip'=>$ip,'created_at'=>time(),'created_time'=>time()];

        foreach($data as $key => $value)
        {
            $adminLoginLog->$key = $value;
        }

        $result = $adminLoginLog->save();

        if(!$result)
        {
            throw new CommonException('AdminLogoutLogError');
        }
    }
}
