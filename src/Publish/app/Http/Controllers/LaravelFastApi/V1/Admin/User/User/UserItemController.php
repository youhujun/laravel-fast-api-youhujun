<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: lak 15931400746@163.com
 * @Date: 2023-08-14 11:12:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-14 12:30:25
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserItemController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\SelectItem\GetDefaultUserDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\SelectItem\FindUserDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacade;

/**
 * @see  \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacadeService
 */
class UserItemController extends Controller
{
    /**
    * 获取默认用户选项
    *
    * @param Request $request
    * @return void
    */
    public function getDefaultUser(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetDefaultUserDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserItemFacade::getDefaultUser($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 查找选项
     *
     * @param Request $request
     * @return void
     */
    public function findUser(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new FindUserDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserItemFacade::findUser($requestDTO, $adminObject);
        }

        return $result;
    }
}
