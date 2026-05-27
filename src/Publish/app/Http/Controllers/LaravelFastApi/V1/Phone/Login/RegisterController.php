<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 09:55:14
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-24 15:32:12
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\Login\RegisterController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Register\SendPhoneRegisterCodeDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Register\UserRegisterDTO;
use App\Facades\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacade;

/**
 *  @see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacadeService
 */
class RegisterController extends Controller
{
    /**
     * 发送用户注册码
     */
    public function sendPhoneRegisterCode(Request $request)
    {
        $result = code(\config('phone.sendUserRegisterCodeError'));

        $requestDTO = (new SendPhoneRegisterCodeDTO())->validate($request->all());

        //p($requestDTO);die;

        $result = PhoneRegisterFacade::sendUserRegisterCode($requestDTO);

        return $result;
    }

    /**
     * 用户注册
     */
    public function userRegister(Request $request)
    {
        $result = code(\config('phone.userRegisterError'));

        $requestDTO = (new UserRegisterDTO())->validate($request->all());

        //p($requestDTO);die;

        $result = PhoneRegisterFacade::userRegister($requestDTO);

        return $result;
    }
}
