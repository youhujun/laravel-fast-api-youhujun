<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 16:18:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 05:15:21
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Log\UserLogController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\GetUserLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\DeleteUserLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\MultipleDeleteUserLoginLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\GetUserEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\DeleteUserEventLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Log\UserLog\MultipleDeleteUserEventLogDTO;
use App\Facades\LaravelFastApi\V1\Admin\Log\AdminUserLogFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Log\AdminUserLogFacadeService
 */
class UserLogController extends Controller
{
    /*
    * 获取登录日志
    *
    * @param Request $request
    * @return void
    */
    public function getUserLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLogFacade::getUserLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLogFacade::deleteUserLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserLoginLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteUserLoginLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLogFacade::multipleDeleteUserLoginLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 获取事件日志
     *
     * @return void
     */
    public function getUserEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLogFacade::getUserEventLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteUserEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLogFacade::deleteUserEventLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserEventLog(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MultipleDeleteUserEventLogDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserLogFacade::multipleDeleteUserEventLog($requestDTO, $adminObject);
        }

        return $result;
    }
}
