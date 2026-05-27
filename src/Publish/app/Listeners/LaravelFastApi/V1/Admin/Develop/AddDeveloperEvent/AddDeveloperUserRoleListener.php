<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 02:28:40
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperUserRoleListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Jobs\Common\V1\Es\Develop\EsAddDeveloperRoleJob;

use function PHPSTORM_META\map;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperUserRoleListener
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
        $isTransation = $event->isTransation;

        $roleCollection = Role::where('type', 10)->get();

        $user_role_union_data_array = $roleCollection->map(function ($roleObject) use ($userObject) {
            return [
                'user_role_union_uid' => get_snow_flake_id(),
                'user_uid' => $userObject->biz_id,
                'role_id' => $roleObject->id,
                'type' => 10,
            ];
        })->toArray();

        // p($user_role_union_data_array);
        // die;

        $result = ShardHelperFacade::insertBatchWithShard(UserRoleUnion::class, $user_role_union_data_array);

        if (!$result) {
            if ($isTransation) {
                throw new CommonException('AddDeveloperRoleError');
            }
        }

        EsAddDeveloperRoleJob::dispatch($userObject)->delay(now()->addSeconds(7));
    }
}
