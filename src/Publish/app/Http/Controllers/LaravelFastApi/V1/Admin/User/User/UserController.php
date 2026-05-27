<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:54:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 15:45:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\GetUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\AddUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDisableUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\DeleteUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\MultipleDeleteUserDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserFacadeService
 */
class UserController extends Controller
{
    public function test()
    {
        echo 'UserController test';
    }

    /**
     * 获取用户列表
     *
     * @param  Request $request
     */
    public function getUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserDTO())->validate($request->all());

            $result = AdminUserFacade::getUser($adminObject, $requestDTO);
        }

        return $result;
    }


    /**
     * 添加用户
     *
     * @param AddUserRequest $request
     * @return void
     */
    public function addUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddUserDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminUserFacade::addUser($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DisableUserDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserFacade::disableUser($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 批量禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDisableUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDisableUserDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserFacade::multipleDisableUser($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return void
     */
    public function deleteUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminUserFacade::deleteUser($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteUserDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserFacade::multipleDeleteUser($adminObject, $requestDTO);
        }

        return $result;
    }
}
