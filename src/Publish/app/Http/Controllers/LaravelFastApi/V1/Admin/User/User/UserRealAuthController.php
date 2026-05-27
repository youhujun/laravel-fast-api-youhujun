<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-13 16:43:07
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserRealAuthController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\GetUserRealAuthApplyDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\RealAuthUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\GetUserIdCardDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\RealAuth\SetUserIdCardDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacadeService
 */
class UserRealAuthController extends Controller
{
    /**
     * 获取用户实名认证申请
     *
     * @param Request $request
     * @return void
     */
    public function getUserRealAuthApply(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserRealAuthApplyDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserRealAuthFacade::getUserRealAuthApply($requestDTO, $adminObject);
        }

        return $result;
    }
    /**
     * 实名认证审核
     *
     * @param Request $request
     * @return void
     */
    public function realAuthUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new RealAuthUserDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserRealAuthFacade::realAuthUser($requestDTO, $adminObject);
        }

        return $result;
    }



    /**
     * 获取用户身份证
     *
     * @param Request $request
     * @return void
     */
    public function getUserIdCard(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserIdCardDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserRealAuthFacade::getUserIdCard($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
    * 设置用户身份证
    *
    * @param Request $request
    * @return void
    */
    public function setUserIdCard(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new SetUserIdCardDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserRealAuthFacade::setUserIdCard($requestDTO, $adminObject);
        }

        return $result;
    }
}
