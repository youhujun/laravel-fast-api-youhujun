<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:54:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 21:28:01
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\AdministratorController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\GetDefaultAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\FindAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\AddAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\GetAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\UpdateAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\DisableAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\MultipleDisableAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\DeleteAdminDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Administrator\MultipleDeleteAdminDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacadeService
 */
class AdministratorController extends Controller
{
    /**
     * 获取所有管理用户
     *
     * @param Request $request
     * @return void
     */
    public function getDefaultAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetDefaultAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::getDefaultAdmin($requestDTO);
        }

        return $result;
    }

    /**
     * 查找管理员
     *
     * @return void
     *
     */
    public function findAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new FindAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::findAdmin($requestDTO, $adminObject);
        }

        return $result;
    }



    //获取管理员列表
    public function getAdmin(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new GetAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::getAdmin($requestDTO);
        }

        return $result;
    }

    /**
     * 添加管理员
     *
     * @param AddAdminRequest $request
     * @return void
     */
    public function addAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new AddAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            
            $result = AdministratorFacade::addAdmin($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改管理员
     *
     * @param UpdateAdminRequest $request
     * @return void
     */
    public function updateAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new UpdateAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::updateAdmin($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DisableAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::disableAdmin($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDisableAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MultipleDisableAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::multipleDisableAdmin($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::deleteAdmin($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdmin(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MultipleDeleteAdminDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdministratorFacade::multipleDeleteAdmin($requestDTO, $adminObject);
        }

        return $result;
    }
}
