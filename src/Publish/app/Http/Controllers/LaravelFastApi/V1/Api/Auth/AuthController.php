<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-19 01:33:08
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 09:20:31
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Api\Auth\AuthController.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\DTOs\Api\V1\Auth\GetAccessTokenDTO;
use App\Facades\Common\V1\Api\Auth\ApiAuthFacade;

class AuthController extends Controller
{
    /**
     * Undocumented function
     *
     * @param  Request $request
     */
    public function getAccessToken(Request $request)
    {
        $result = code(\config('common_code.ApiAuthError'));

        $getAccessTokenDTO = (new GetAccessTokenDTO())->validate($request->all());

        plog(['params' => $getAccessTokenDTO->toArray(),'service' => 'youhubase'], 'ApiAuth', 'GetAccessToken');

        ApiAuthFacade::verifySign($getAccessTokenDTO->toArray());

        $accessToken = ApiAuthFacade::getTempToken('youhubase_');

        $result = ['code' => 0,'msg' => '验签通过','access_token' => $accessToken];

        return $result;
    }
}
