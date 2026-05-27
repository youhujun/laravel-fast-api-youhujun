<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-14 09:09:59
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-12 12:09:22
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserDetailsController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserPhoneDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserRealNameDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserNickNameDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserSexDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\ChangeUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\UpdateUserBirthdayTimeDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\GetUserQrcodeDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\ResetUserPasswordDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\User\Details\MakeUserQrcodeDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserDetailsFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserDetailsFacadeService
 */
class UserDetailsController extends Controller
{
    /**
     * 修改用户手机号
     *
     * @param Request $request
     * @return void
     */
    public function updateUserPhone(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserPhoneDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::updateUserPhone($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
    * 修改用户真实姓名
    *
    * @param Request $request
    * @return void
    */
    public function updateUserRealName(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserRealNameDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::updateUserRealName($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
    * 修改用户昵称
    *
    * @param Request $request
    * @return void
    */
    public function updateUserNickName(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserNickNameDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::updateUserNickName($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改用户性别
     *
     * @param Request $request
     * @return void
     */
    public function updateUserSex(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserSexDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::updateUserSex($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 更改用户级别
     *
     * @param Request $request
     * @return void
     */
    public function changeUserLevel(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new ChangeUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserDetailsFacade::changeUserLevel($requestDTO, $adminObject);
        }

        return $result;
    }




    /**
     * 修改用户生日
     *
     * @param Request $request
     * @return void
     */
    public function updateUserBirthdayTime(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserBirthdayTimeDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::updateUserBirthdayTime($requestDTO, $adminObject);
        }
        return $result;
    }

    /**
     * 重置用户密码
     *
     * @return void
     */
    public function resetUserPassword(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new ResetUserPasswordDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserDetailsFacade::resetUserPassword($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
    *获取用户二维码
    */
    public function getUserQrcode(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetUserQrcodeDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserDetailsFacade::getUserQrcode($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 生成用户二维码
     *
     * @param  Request $request
     */
    public function makeUserQrcode(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MakeUserQrcodeDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserDetailsFacade::makeUserQrcode($requestDTO, $adminObject);
        }

        return $result;
    }
}
