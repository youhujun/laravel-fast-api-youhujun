<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 23:59:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 11:34:31
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\AddSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\GetSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\UpdateSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\DeleteSystemConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfig\MultipleDeleteSystemConfigDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminSystemConfigFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminSystemConfigFacadeService
 */
class SystemConfigController extends Controller
{
    /**
     * 查询
     */
    public function getSystemConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetSystemConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminSystemConfigFacade::getSystemConfig($requestDTO);
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addSystemConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddSystemConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminSystemConfigFacade::addSystemConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateSystemConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateSystemConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminSystemConfigFacade::updateSystemConfig($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteSystemConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteSystemConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminSystemConfigFacade::deleteSystemConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteSystemConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteSystemConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminSystemConfigFacade::multipleDeleteSystemConfig($requestDTO, $adminObject);
        }

        return $result;
    }
}
