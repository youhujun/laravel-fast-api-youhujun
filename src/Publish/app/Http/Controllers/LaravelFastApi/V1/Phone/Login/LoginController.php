<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 10:32:47
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 21:12:13
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\Login\LoginController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByPhonePasswordDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendVerifyCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByPhoneCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\SendPasswordCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\RestPasswordByPhoneDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\LoginByUserIdDTO;
use App\Facades\LaravelFastApi\V1\Phone\Login\PhoneLoginFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService
 */
class LoginController extends Controller
{
    /**
     * 通过手机号 密码登录
     *
     * @param LoginRequest $request
     * @return void
     */
    public function loginByPhonePassword(Request $request)
    {
        $result = code(\config('phone_code.LoginByUserError'));

        $requestDTO = (new LoginByPhonePasswordDTO())->validate($request->all());
        //p($validated);die
        $result = PhoneLoginFacade::loginByPhonePassword($requestDTO);

        return $result;
    }

    /**
    * 发送手机验证码
    *
    * @param Request $request
    * @return void
    */
    public function sendVerifyCode(Request $request)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $requestDTO = (new SendVerifyCodeDTO())->validate($request->all());

        $result = PhoneLoginFacade::sendVerifyCode($requestDTO);

        return $result;
    }

    /**
    * 通过手机号验证码登录
    *
    * @param Request $request
    * @return void
    */
    public function loginByPhoneCode(Request $request)
    {
        $result = code(\config('phone_code.LoginByPhoneError'));

        $requestDTO = (new LoginByPhoneCodeDTO())->validate($request->all());

        $result = PhoneLoginFacade::loginByPhoneCode($requestDTO);

        return $result;
    }

    /**
    * 发送手机验证码 忘记密码
    *
    * @param Request $request
    * @return void
    */
    public function sendPasswordCode(Request $request)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $requestDTO = (new SendPasswordCodeDTO())->validate($request->all());

        $result = PhoneLoginFacade::sendPasswordCode($requestDTO);

        return $result;
    }

    /**
     * 重置手机密码
     *
     * @param  Request $request
     */
    public function restPasswordByPhone(Request $request)
    {
        $result = code(\config('phone_code.RestPasswordByPhoneError'));

        $requestDTO = (new RestPasswordByPhoneDTO())->validate($request->all());

        $result = PhoneLoginFacade::restPasswordByPhone($requestDTO);

        return $result;
    }

    /**
     * app 一键登录注册
     *
     * @param Request $request
     * @return void
     */
    // public function univerifyLogin(Request $request)
    // {
    //     $validated = $request->validate(
    //     [
    //         'provider'=>['bail',new Required,new CheckString],
    //         'openid'=>['bail',new Required,new CheckString],
    //         'access_token'=>['bail',new Required,new CheckString],
    //     ],
    //     [
    //         'provider.required' => '必须有provider',
    //         'openid.required' => '必须有openid',
    //         'access_token.required' => '必须有access_token',
    //     ]);

    //     $validated['ip'] = $request->getClientIp();

    //     //Log::debug(['$validated'=> $validated]);

    //     //p($validated);die;

    //     $result = PhoneLoginFacade::univerifyLogin($validated);

    //     return $result;

    // }

    /**
     * 通过用户id登录,开发测试用
     *
     * @param  Request $request
     */
    public function loginByUserId(Request $request)
    {
        $requestDTO = (new LoginByUserIdDTO())->validate($request->all());

        // p($requestDTO);
        // die;

        $result = PhoneLoginFacade::loginByUserId($requestDTO);

        return $result;
    }
}
