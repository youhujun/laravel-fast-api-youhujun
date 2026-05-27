<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 15:08:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-11 21:42:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAddressController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\AddUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\GetUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\DeleteUserAddressDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Address\SetDefaultUserAddressDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacadeService
 */
class UserAddressController extends Controller
{
    /**
     * 添加用户地址
     *
     * @param Request $request
     * @return void
     */
    public function addUserAddress(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddUserAddressDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserAddressFacade::addUserAddress($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 获取用户地址
     *
     * @param Request $request
     * @return void
     */
    public function getUserAddress(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserAddressDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserAddressFacade::getUserAddress($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除用户地址
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserAddress(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserAddressDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserAddressFacade::deleteUserAddress($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 设置用户默认地址
     *
     * @param Request $request
     * @return void
     */
    public function setDefaultUserAddress(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new SetDefaultUserAddressDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserAddressFacade::setDefaultUserAddress($requestDTO, $adminObject);
        }

        return $result;
    }
}
