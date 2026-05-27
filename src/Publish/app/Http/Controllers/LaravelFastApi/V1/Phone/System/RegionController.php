<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-02 18:12:03
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-27 03:25:36
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\System\RegionController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\System\Region\GetRegionByIdDTO;
use App\Facades\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacadeService
 */
class RegionController extends Controller
{
    /**
     * 手机替换
     *
     * @param Request $request
     * @return void
     */
    public function getRegionById(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new GetRegionByIdDTO())->validate($request->all());

            $result = PhoneRegionFacade::getRegionById($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 获取树形地区
     *
     * @param  Request $request
     */
    public function getTreeRegions(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $result = PhoneRegionFacade::getTreeRegions($userObject);
        }

        return $result;
    }
}
