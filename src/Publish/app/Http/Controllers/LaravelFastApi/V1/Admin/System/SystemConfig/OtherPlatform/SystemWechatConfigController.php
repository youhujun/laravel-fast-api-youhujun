<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 14:57:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 13:11:54
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\GetSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\AddSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\UpdateSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\DeleteSystemWechatConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\WechatConfig\MultipleDeleteSystemWechatConfigDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacadeService
 */
class SystemWechatConfigController extends Controller
{
    /**
     * 查询
     */
    public function getSystemWechatConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetSystemWechatConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemWechatConfigFacade::getSystemWechatConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addSystemWechatConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddSystemWechatConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemWechatConfigFacade::addSystemWechatConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateSystemWechatConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateSystemWechatConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemWechatConfigFacade::updateSystemWechatConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteSystemWechatConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteSystemWechatConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = SystemWechatConfigFacade::deleteSystemWechatConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteSystemWechatConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteSystemWechatConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;


            $result = SystemWechatConfigFacade::multipleDeleteSystemWechatConfig($requestDTO, $adminObject);
        }

        return $result;
    }
}
