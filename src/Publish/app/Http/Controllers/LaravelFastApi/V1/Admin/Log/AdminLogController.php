<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 16:18:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 03:45:03
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\GetAdminEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\GetAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\DeleteAdminEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\DeleteAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\MultipleDeleteAdminLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\AdminLog\MultipleDeleteAdminEventLogDTO;
use App\Facades\LaravelFastApi\V1\Admin\Log\AdminLogFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminLogFacadeService
 */
class AdminLogController extends Controller
{
    /**
     * 获取登录日志
     *
     * @param Request $request
     * @return void
     */
    public function getAdminLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetAdminLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::getAdminLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdminLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteAdminLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::deleteAdminLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdminLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MultipleDeleteAdminLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::multipleDeleteAdminLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 获取事件日志
     *
     * @return void
     */
    public function getAdminEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetAdminEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::getAdminEventLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdminEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteAdminEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::deleteAdminEventLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdminEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MultipleDeleteAdminEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLogFacade::multipleDeleteAdminEventLog($requestDTO, $adminObject);
        }

        return $result;
    }
}
