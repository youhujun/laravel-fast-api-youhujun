<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 17:22:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-11 13:35:11
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAccountController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\SetUserAccountDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\GetUserAccountLogDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Account\GetUserAccountInfoDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserAccountFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAccountFacadeService
 */
class UserAccountController extends Controller
{
    /**
     * 操作用户账户
     *
     * @param Request $request
     * @return void
     */
    public function setUserAccount(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new SetUserAccountDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserAccountFacade::setUserAccount($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 获取用户账户日志
     *
     * @param Request $request
     * @return void
     */
    public function getUserAccountLog(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserAccountLogDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserAccountFacade::getUserAccountLog($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 获取用户账户信息
     *
     * @param  Request $request
     */
    public function getUserAccountInfo(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserAccountInfoDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminUserAccountFacade::getUserAccountInfo($requestDTO, $adminObject);
        }

        return $result;
    }
}
