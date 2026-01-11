<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 14:35:57
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\CommonEvent\CommonEventListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Listeners\LaravelFastApi\V1\Admin\CommonEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;

use Illuminate\Support\Facades\Route;
/**
 * @see \App\Events\Admin\CommonEvent
 */
class CommonEventListener
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

        $logData = $event->logData;

        $eventCode = $event->eventCode;

        $isTransation = $event->isTransation;

        $adminEventLog = new AdminEventLog;

        $adminEventLog->admin_id = $admin->id;
        $adminEventLog->event_route_action = Route::currentRouteAction();

        $adminEventLog->event_name = \config("admin_event.{$eventCode}.info");

        $adminEventLog->event_type = \config("admin_event.{$eventCode}.code");

        $adminEventLog->event_code = \config("admin_event.{$eventCode}.event");

        if($logData)
        {
             $adminEventLog->note = \json_encode($logData);
        }
        else
        {
            $adminEventLog->note = \json_encode($admin);
        }

        $adminEventLog->created_time = time();
        $adminEventLog->created_at = time();

        $result = $adminEventLog->save();

        if(!$result)
        {
            if($isTransation)
            {
                DB::rollBack();
            }

            throw new CommonException("{$eventCode}EventError");
        }

    }
}
