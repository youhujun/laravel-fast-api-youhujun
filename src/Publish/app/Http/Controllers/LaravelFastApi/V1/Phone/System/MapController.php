<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 14:46:01
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-14 16:18:28
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\System\MapController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Phone\System\Map\GetLocationRegionByH5DTO;
use App\Facades\LaravelFastApi\V1\Phone\System\PhoneMapFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacadeService
 */
class MapController extends Controller
{
    /**
    * 获取默认选项
    */
    public function getLocationRegionByH5(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new GetLocationRegionByH5DTO())->validate($request->all());

            $result = PhoneMapFacade::getLocationRegionByH5($requestDTO, $userObject);
        }

        //p($validated);die;
        return $result;
    }
}
