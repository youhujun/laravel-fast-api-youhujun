<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-29 14:47:48
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 13:38:20
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent\AddUserLocationLogListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\Phone\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\Log\UserLocationLog;
use App\Jobs\Common\V1\Es\Location\EsAddUserLocationLogJob;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\Location\UserLocationLogEvent
 */
class AddUserLocationLogListener
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
        $validated = $event->validated;
        $address = $event->address;
        $isTransation = $event->isTransation;

        ['latitude' => $latitude,'longitude' => $longitude] = $validated;

        $insertDataArray = [
            'user_location_log_uid' => get_snow_flake_id(),
            'user_uid' => $userObject->user_uid,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'type' => 10,
            'data_type' => 1,
            'address' => $address
        ];

        $userLocationLogObject = ShardHelperFacade::createWithShard(UserLocationLog::class, $userObject->user_uid, $insertDataArray);

        if (!isset($userLocationLogObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddUserLocationLogError');
        }

        EsAddUserLocationLogJob::dispatch($userObject, $userLocationLogObject)->delay(now()->addSeconds(3));
    }
}
