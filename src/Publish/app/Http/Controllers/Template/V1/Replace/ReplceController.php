<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 16:39:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-07 14:27:12
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\Template\V1\Replace\ReplceController.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Controllers\Template\V1\Replace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\Template\V1\Replace\GetReplaceDTO;
use App\DTOs\Template\V1\Replace\AddReplaceDTO;
use App\DTOs\Template\V1\Replace\UpdateReplaceDTO;
use App\DTOs\Template\V1\Replace\DisableReplaceDTO;
use App\DTOs\Template\V1\Replace\DeleteReplaceDTO;
use App\DTOs\Template\V1\Replace\MultipleDisableReplaceDTO;
use App\DTOs\Template\V1\Replace\MultipleDeleteReplaceDTO;
use App\Facades\Template\V1\Replace\EsReplaceFacade;

/**
 * @see \App\Services\Facade\Template\V1\Replace\EsReplaceFacadeService
 */
class ReplceController extends Controller
{
    public function test()
    {
        echo 'ReplaceController test';
    }

    /**
     * 获取用户列表
     *
     * @param  Request $request
     */
    public function getReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetReplaceDTO())->validate($request->all());

            p($requestDTO);
            die;

            $result = EsReplaceFacade::getReplace($adminObject, $requestDTO);
        }

        return $result;
    }


    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddReplaceDTO())->validate($request->all());

            // p($addReplaceDTO);
            // die;
            $result = EsReplaceFacade::addReplace($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
    * 修改
    *
    * @param Request $request
    * @return void
    */
    public function updateReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateReplaceDTO())->validate($request->all());

            // p($updateReplaceDTO);
            // die;
            $result = EsReplaceFacade::updateReplace($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new DisableReplaceDTO())->validate($request->all());

            // p($disableReplaceDTO);
            // die;

            $result = EsReplaceFacade::disableReplace($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 批量禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDisableReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDisableReplaceDTO())->validate($request->all());

            // p($multipleDisableReplaceDTO);
            // die;

            $result = EsReplaceFacade::multipleDisableReplace($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return void
     */
    public function deleteReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteReplaceDTO())->validate($request->all());
            // p($deleteReplaceDTO);
            // die;
            $result = EsReplaceFacade::deleteReplace($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteReplace(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forReplace($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteReplaceDTO())->validate($request->all());

            // p($multipleDeleteReplaceDTO);
            // die;

            $result = EsReplaceFacade::multipleDeleteReplace($adminObject, $requestDTO);
        }

        return $result;
    }
}
