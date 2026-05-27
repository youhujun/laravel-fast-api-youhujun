<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-17 10:21:04
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 19:25:06
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level\UserLevelController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckDbUnique;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DefaultUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\FindUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\GetUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\AddUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\UpdateUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DeleteUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\MultipleDeleteUserLevelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\AddUserLevelItemUnionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\UpdateUserLevelItemUnionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\UserLevel\DeleteUserLevelItemUnionDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacadeService
 */
class UserLevelController extends Controller
{
    /**
     * 获取默认选项
     */
    public function defaultUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $userObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new DefaultUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminUserLevelFacade::defaultUserLevel($requestDTO);
        }

        return $result;
    }

    /**
     * 查找选项
     *
     * @param Request $request
     * @return void
     */
    public function findUserLevel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new FindUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::findUserLevel($requestDTO);
        }

        return $result;
    }


    /**
     * 获取用户级别列表
     */
    public function getUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $userObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new GetUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::getUserLevel($requestDTO);
        }

        return $result;
    }

    /**
     * 添加用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function addUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $userObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new AddUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserLevelFacade::addUserLevel($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 修改用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function updateUserLevel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserLevelDTO($id))->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::updateUserLevel($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 删除用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLevel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserLevelFacade::deleteUserLevel($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 批量删除用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserLevel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteUserLevelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::multipleDeleteUserLevel($requestDTO, $userObject);
        }

        return $result;
    }


    /**
     * 添加用户级别配置项值
     *
     * @param Request $request
     * @return void
     */
    public function addUserLevelItemUnion(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $userObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new AddUserLevelItemUnionDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminUserLevelFacade::addUserLevelItemUnion($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateUserLevelItemUnion(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new UpdateUserLevelItemUnionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::updateUserLevelItemUnion($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLevelItemUnion(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new DeleteUserLevelItemUnionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminUserLevelFacade::deleteUserLevelItemUnion($requestDTO, $userObject);
        }

        return $result;
    }
}
