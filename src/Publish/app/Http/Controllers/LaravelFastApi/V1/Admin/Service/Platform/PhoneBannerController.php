<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 15:24:29
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 02:08:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform\PhoneBannerController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\GetPhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\AddPhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\UpdatePhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\DeletePhoneBannerDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Platform\PhoneBanner\MultipleDeletePhoneBannerDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Platform\AdminPhoneBannerFacadeService
 */
class PhoneBannerController extends Controller
{
    /**
     * 获取轮播图
     *
     * @param Request $request
     * @return void
     */
    public function getPhoneBanner(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetPhoneBannerDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminPhoneBannerFacade::getPhoneBanner($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加首页轮播图
     *
     * @param Request $request
     * @return void
     */
    public function addPhoneBanner(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddPhoneBannerDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminPhoneBannerFacade::addPhoneBanner($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改轮播图
     * @param {Request} $request
     * @return {*}
     */
    public function updatePhoneBanner(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneBannerDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminPhoneBannerFacade::updatePhoneBanner($requestDTO, $adminObject);
        }

        return $result;
    }


    /**
     * 删除轮播图
     * @param {Request} $request
     * @return {*}
     */
    public function deletePhoneBanner(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeletePhoneBannerDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminPhoneBannerFacade::deletePhoneBanner($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     */
    public function multipleDeletePhoneBanner(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeletePhoneBannerDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminPhoneBannerFacade::multipleDeletePhoneBanner($requestDTO, $adminObject);
        }

        return $result;
    }
}
