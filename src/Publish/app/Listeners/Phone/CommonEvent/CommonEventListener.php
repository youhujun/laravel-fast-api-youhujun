<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-02 10:41:22
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 11:30:27
 * @FilePath: \app\Listeners\Phone\CommonEvent\CommonEventListener.php
 */

namespace App\Listeners\Phone\CommonEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;

use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;

use Illuminate\Support\Facades\Route;

/**
 * @see \App\Events\Phone\CommonEvent
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
        $user = $event->user;

        $logData = $event->logData;

        $eventCode = $event->eventCode;

        $isTransation = $event->isTransation;

        $userEventLog = new UserEventLog;

        $userEventLog->user_id =$user->id;
        $userEventLog->event_route_action = Route::currentRouteAction();

        $userEventLog->event_name = \config("phone_event.{$eventCode}.info");;

        $userEventLog->event_type = \config("phone_event.{$eventCode}.code");

        $userEventLog->event_code = \config("phone_event.{$eventCode}.event");

        if($logData)
        {
            $userEventLog->note = \json_encode($logData);
        }
        else
        {
            $userEventLog->note = \json_encode($user);
        }

        $userEventLog->created_time = time();
        $userEventLog->created_at = time();

         $result =$userEventLog->save();

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
