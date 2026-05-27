<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-07 08:09:43
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 03:11:22
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Jobs\Common\V1\Es\Admin\EsAddAdminJob;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperAdminListener
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

        $user_uid = $userObject->biz_id;
        Admin::bindShardBusinessId($user_uid);

        $adminObject = Admin::create([
            'admin_uid' => get_snow_flake_id(),
            'user_uid' => $user_uid,
            'remember_token' => null,
            'phone_area_code' => '+86',
            'phone' => null,
            'account_name' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'account_status' => 1
        ]);

        if (!isset($adminObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddDevelopAdminError');
        }

        

        EsAddAdminJob::dispatch($userObject)->delay(now()->addSeconds(6));
    }
}
