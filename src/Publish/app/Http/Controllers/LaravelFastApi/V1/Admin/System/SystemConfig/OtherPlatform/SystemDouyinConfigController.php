<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 14:57:27
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 14:35:18
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\GetSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\AddSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\UpdateSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\DeleteSystemDouyinConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\DouyinConfig\MultipleDeleteSystemDouyinConfigDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigFacadeService
 */
class SystemDouyinConfigController extends Controller
{
    /**
     * 查询
     */
    public function getSystemDouyinConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetSystemDouyinConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemDouyinConfigFacade::getSystemDouyinConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addSystemDouyinConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddSystemDouyinConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemDouyinConfigFacade::addSystemDouyinConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateSystemDouyinConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateSystemDouyinConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemDouyinConfigFacade::updateSystemDouyinConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteSystemDouyinConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteSystemDouyinConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemDouyinConfigFacade::deleteSystemDouyinConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteSystemDouyinConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteSystemDouyinConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemDouyinConfigFacade::multipleDeleteSystemDouyinConfig($requestDTO, $adminObject);
        }

        return $result;
    }
}
