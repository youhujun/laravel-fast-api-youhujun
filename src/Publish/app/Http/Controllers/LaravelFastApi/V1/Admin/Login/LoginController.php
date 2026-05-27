<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 22:41:13
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 20:54:12
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Login\AdminLoginDTO;
use App\Facades\LaravelFastApi\V1\Admin\Login\AdminLoginFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacadeService
 */
class LoginController extends Controller
{
    /**
     * 获取默认选项
     */
    public function login(Request $request)
    {
        $loginDTO = (new AdminLoginDTO())->validate($request->all());

        $result = AdminLoginFacade::authLogin($loginDTO);

        return $result;
    }

    /**
     * 管理员退出
     *
     * @param  Request $request
     */
    public function logout(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result = AdminLoginFacade::logout($adminObject);
        }
        return $result;
    }
}
