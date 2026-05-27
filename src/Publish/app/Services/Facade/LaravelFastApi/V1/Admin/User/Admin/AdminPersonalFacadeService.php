<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-08 14:10:45
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 13:29:03
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Personal\UpdatePhoneDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Personal\UpdatePasswordDTO;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class AdminPersonalFacadeService
{
    public function test()
    {
        echo "AdminPersonalFacadeService test";
    }

    /**
     * 确认修改头像
     */
    public function updateAvatar(Admin $adminObject)
    {
        Redis::hdel("admin_info:admin_info", $adminObject->biz_id);

        $result = code(['code' => 0,'msg' => '修改头像成功!']);

        return $result;
    }

    /**
     * 修改手机号
     */
    public function updatePhone(UpdatePhoneDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000,'msg' => '修改手机号失败'];

        $indexName = config('common_es.indices.user.admins');

        $esAdminObject = EsQueryFacade::index($indexName)->where('admin_uid', $adminObject->biz_id)->get()->first();

        if (empty($esAdminObject)) {
            throw new CommonException('ServiceBusyError');
        }

        $updateDataArray = [
            'phone' => $requestDTO->phone
        ];

        $updateResult = $adminObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateAdminPhoneError');
        }

        $adminObject = $adminObject->fresh();

        $indexName = config('common_es.indices.user.admins');

        $updateDataArray = [
            'phone' => $adminObject->phone,
            'password' => $adminObject->password,
            'updated_at' => $adminObject->updated_at,
            'updated_time' => $adminObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新个人中心管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'EsUpdateAdminJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateAdminPhone');

        $result = code(['code' => 0,'msg' => '修改手机号成功']);

        return $result;
    }

    /**
     * 修改密码
     *
     * @param  [type] $validated
     * @param  [type] $adminObject
     */
    public function updatePassword(UpdatePasswordDTO $requestDTO, Admin $adminObject)
    {
        $result = ['code' => 10000,'msg' => '修改密码失败'];

        $indexName = config('common_es.indices.user.admins');

        $esAdminObject = EsQueryFacade::index($indexName)->where('admin_uid', $adminObject->biz_id)->get()->first();

        if (empty($esAdminObject)) {
            throw new CommonException('ServiceBusyError');
        }

        if ($requestDTO->password !== $requestDTO->repass) {
            throw new CommonException('PasswordNotMatchError');
        }

        $updateDataArray = [
            'password' => Hash::make($requestDTO->password)
        ];

        $updateResult = $adminObject->updateWithShard($updateDataArray);

        if (!$updateResult) {
            throw new CommonException('UpdateAdminPasswordError');
        }

        $indexName = config('common_es.indices.user.admins');

        $updateDataArray = [
            'phone' => $adminObject->phone,
            'password' => $adminObject->password,
            'updated_at' => $adminObject->updated_at,
            'updated_time' => $adminObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $adminObject->admin_uid, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] !== 0) {
            plog(['error' => 'es更新个人中心管理员失败','$esResult' => $esResult,'$updateDataArray' => $updateDataArray,'$adminObject' => $adminObject,'$loginAdminObject' => $loginAdminObject], 'EsUpdateAdminJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateAdminPassword');

        $result = code(['code' => 0,'msg' => '修改手机号成功']);

        return $result;
    }
}
