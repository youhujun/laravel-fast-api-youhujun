<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-04 01:49:58
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Develop;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\DTOs\LaravelFastApi\V1\Admin\Develop\AddDevelpDTO;
use App\Events\Common\V1\User\User\CommonUserRegisterEvent;
use App\Events\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract;
use App\Jobs\Common\V1\Es\User\EsAddUserJob;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Develop\DevelopController
 * @see \App\Facades\Admin\Develop\DeveloperFacade
 */
class DeveloperFacadeService
{
    public function test()
    {
        echo "DeveloperFacadeService test";
    }

    /**
     * 添加开发者
     *
     * @param [type] $validated
     * @return void
     */
    public function addDeveloper(Admin $adminObject, AddDevelpDTO $addDevelopDTO)
    {
        $result = code(config('admin_code.AddDeveloperError'));

        $validated = $addDevelopDTO->toArray();

        DB::beginTransaction();

        $user_uid = get_snow_flake_id();

        User::bindShardBusinessId($user_uid);

        $userObject = User::create([
            'user_uid' => $user_uid,
            'source_user_uid' => isset($validated['source_user_uid']) ? $validated['source_user_uid'] : 0,
            'parent_user_uid' => 0,
            'revision' => 0,
            'phone' => null,
            'password' => Hash::make($validated['password']),
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 1,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => $validated['username'],
            'phone_area_code' => '+86',
            'email' => null,
        ]);

        if (!isset($userObject->biz_id)) {
            throw new CommonException('AddDeveloperError');
        }

        $eventParamArray = ['userObject' => $userObject,'adminObject' => $adminObject,'validated' => $validated,'isTransation' => 1];

        //分别传递用户,管理员,数据,是否开启事务,是否开启推荐分销
        CommonUserRegisterEvent::dispatch($eventParamArray);

        //开发者事件
        AddDeveloperEvent::dispatch($eventParamArray);

        //异步映射ES用户数据
        EsAddUserJob::dispatch($userObject)->delay(now()->addSeconds(3));

        //契约业务
        $handleParamArray = ['userObject' => $userObject,'adminObject' => $adminObject,'validated' => $validated];

        app(AddUserHandlerContract::class)->handle($handleParamArray);

        CommonEvent::dispatch($adminObject, $validated, 'AddDeveloper');

        DB::commit();

        $result = code(['code' => 0,'msg' => '添加开发者成功!']);

        return $result;
    }
}
