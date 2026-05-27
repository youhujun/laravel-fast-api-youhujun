<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-07 20:03:22
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 15:37:57
 * @FilePath: \youhu-laravel-api-12\app\Listeners\Admin\CommonEvent\CommonEventListener.php
 */

namespace App\Listeners\Admin\CommonEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog;
use Illuminate\Support\Facades\Route;
use App\Jobs\Common\Admin\CommonEvent\EsAdminEventLogJob;

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
        $adminObject = $event->adminObject;

        $logData = $event->logData;

        $eventCode = $event->eventCode;

        $isTransation = $event->isTransation;

        $adminEventLogArray = [
            'admin_event_log_uid' => get_snow_flake_id(),
            'admin_uid' => $adminObject->biz_id,
            'data_type' => 1,
            'event_route_action' => Route::currentRouteAction(),
            'event_name' => \config("admin_event.{$eventCode}.info") ?? $eventCode,
            'event_type' => \config("admin_event.{$eventCode}.code") ?? $eventCode,
            'event_code' => \config("admin_event.{$eventCode}.event") ?? $eventCode,
            'note' => \json_encode($logData),
        ];
        $adminEventLogObject = ShardHelperFacade::createWithShard(
            AdminEventLog::class,
            $adminObject->biz_id,
            $adminEventLogArray
        );


        if (!isset($adminEventLogObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException("{$eventCode}EventError");
        }

        EsAdminEventLogJob::dispatch($adminEventLogObject)->delay(now()->addSeconds(3));
    }
}
