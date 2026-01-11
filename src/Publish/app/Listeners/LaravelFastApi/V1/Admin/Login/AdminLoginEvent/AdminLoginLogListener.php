<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:54:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-14 23:05:15
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent\AdminLoginLogListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

use App\Exceptions\Admin\CommonException;


use App\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent
 */
class AdminLoginLogListener
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
        $adminLoginLog = new AdminLoginLog;

		$ip = Request::getClientIp();

        $data = ['admin_id'=>$admin->id,'status'=>10,'instruction'=>'管理员登录','ip'=>$ip,'created_at'=>time(),'created_time'=>time()];

        foreach($data as $key => $value)
        {
            $adminLoginLog->$key = $value;
        }

        $result = $adminLoginLog->save();

        if(!$result)
        {
            throw new CommonException('AdminLoginLogError');
        }
    }
}
