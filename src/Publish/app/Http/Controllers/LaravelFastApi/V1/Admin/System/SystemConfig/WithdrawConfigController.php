<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 12:47:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 07:30:09
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfigController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfig\UpdateWithdrawConfigDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacadeService
 */
class WithdrawConfigController extends Controller
{
    /**
    * 获取系统提现配置
    */
    public function getWithdrawConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $result =  AdminWithdrawConfigFacade::getWithdrawConfig($adminObject);
        }

        return $result;
    }

    /**
     * 更新系统提现配置
     */
    public function updateWithdrawConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new UpdateWithdrawConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminWithdrawConfigFacade::updateWithdrawConfig($requestDTO, $adminObject);
        }

        return $result;
    }
}
