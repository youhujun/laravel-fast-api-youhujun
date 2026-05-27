<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-03-05 14:40:01
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\AddVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\GetVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\UpdateVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\DeleteVoiceConfigDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfig\MultipleDeleteVoiceConfigDTO;
use App\Rules\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigVoiceSaveType;
use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacadeService
 */
class VoiceConfigController extends Controller
{
    /**
     * 查询
     */
    public function getVoiceConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetVoiceConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminVoiceConfigFacade::getVoiceConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addVoiceConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddVoiceConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminVoiceConfigFacade::addVoiceConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateVoiceConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateVoiceConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminVoiceConfigFacade::updateVoiceConfig($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteVoiceConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteVoiceConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminVoiceConfigFacade::deleteVoiceConfig($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteVoiceConfig(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteVoiceConfigDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminVoiceConfigFacade::multipleDeleteVoiceConfig($requestDTO, $adminObject);
        }

        return $result;
    }
}
