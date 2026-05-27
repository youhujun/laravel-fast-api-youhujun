<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-02 10:41:22
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 20:29:34
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Phone\CommonEvent\CommonEventListener.php
 */

namespace App\Listeners\Phone\CommonEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Phone\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Log\UserEventLog;
use Illuminate\Support\Facades\Route;
use App\Jobs\Common\Phone\CommonEvent\EsUserEventLogJob;

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
        $userObject = $event->userObject;

        $logData = $event->logData;

        $eventCode = $event->eventCode;

        $isTransation = $event->isTransation;

        $userEventLog = new UserEventLog();

        $userEventLogArray = [
            'user_event_log_uid' => get_snow_flake_id(),
            'user_uid' => $userObject->biz_id,
            'data_type' => 1,
            'event_route_action' => Route::currentRouteAction(),
            'event_name' => \config("admin_event.{$eventCode}.info") ?? $eventCode,
            'event_type' => \config("admin_event.{$eventCode}.code") ?? $eventCode,
            'event_code' => \config("admin_event.{$eventCode}.event") ?? $eventCode,
            'note' => \json_encode($logData),
        ];

        $userEventLogObject = ShardHelperFacade::createWithShard(
            UserEventLog::class,
            $userObject->biz_id,
            $userEventLogArray
        );

        if (!isset($userEventLogObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException("{$eventCode}EventError");
        }

        EsUserEventLogJob::dispatch($userEventLogObject)->delay(now()->addSeconds(3));
    }
}
