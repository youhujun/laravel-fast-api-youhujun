<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 15:32:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-12 21:28:16
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserBankController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\AddUserBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\SetUserDefaultBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\DeleteUserBankDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Bank\GetUserBankDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserBankFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserBankFacadeService
 */
class UserBankController extends Controller
{
    /**
     * 添加银行卡
     *
     * @param Request $request
     * @return void
     */
    public function addUserBank(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddUserBankDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserBankFacade::addUserBank($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 设置默认银行卡
     *
     * @param Request $request
     * @return void
     */
    public function setUserDefaultBank(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new SetUserDefaultBankDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminUserBankFacade::setUserDefaultBank($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 删除用户银行卡
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserBank(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserBankDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserBankFacade::deleteUserBank($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
    * 获取用户 银行卡
    *
    * @param Request $request
    * @return void
    */
    public function getUserBank(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserBankDTO())->validate($request->all());
            // p($requestDTO);
            // die;

            $result = AdminUserBankFacade::getUserBank($requestDTO, $adminObject);
        }

        return $result;
    }
}
