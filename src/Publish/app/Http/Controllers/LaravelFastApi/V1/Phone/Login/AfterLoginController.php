<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-28 21:56:45
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-25 01:25:34
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\Login\AfterLoginController.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\GetUserInfoDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendBindCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\BindPhoneDTO;
use App\Facades\LaravelFastApi\V1\Phone\Login\PhoneLoginFacade;

/**
 *@see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService
*/
class AfterLoginController extends Controller
{
    /**
     * 检测是否定登录
     *
     * @return void
     */
    public function checkIsLogin(Request $request)
    {
        $result = code(['code' => 0,'msg' => '用户已经成功登录!']);

        return $result;
    }

    /**
     * 获取用户信息
     *
     * @param Request $request
     * @return void
     */
    public function getUserInfo(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        //p($userObject);die;

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new GetUserInfoDTO())->validate($request->all());

            /**
             *@see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService
             */
            $result = PhoneLoginFacade::getUserInfo($requestDTO, $userObject);
        }

        return $result;
    }

    /**
    * 发送手机验证码
    *
    * @param Request $request
    * @return void
    */
    public function sendBindCode(Request $request)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new SendBindCodeDTO())->validate($request->all());

            $result = PhoneLoginFacade::sendBindCode($requestDTO);
        }

        return $result;
    }

    /**
     * 微信登录绑定手机号
     *
     * @param  Request $request
     */
    public function bindPhone(Request $request)
    {
        $userObject = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new BindPhoneDTO())->validate($request->all());

            $result = PhoneLoginFacade::bindPhone($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 用户退出
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $userObject = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            /**
             *@see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService
             */
            $result = PhoneLoginFacade::logout($userObject);
        }

        return $result;
    }
}
