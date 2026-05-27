<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 16:26:05
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-11 11:19:11
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserTeamController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Team\GetUserSourceDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Team\GetUserLowerTeamDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacadeService
 */
class UserTeamController extends Controller
{
    /**
    * 获取用户的上级用户(推荐用户)
    *
    * @param Request $request
    * @return void
    */
    public function getUserSource(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserSourceDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserTeamFacade::getUserSource($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 获取用户下级团队
     */
    public function getUserLowerTeam(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserLowerTeamDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserTeamFacade::getUserLowerTeam($requestDTO, $adminObject);
        }

        return $result;
    }
}
