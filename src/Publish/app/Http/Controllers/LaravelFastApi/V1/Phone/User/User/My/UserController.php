<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-04 16:02:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 16:05:11
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\UserController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\User\UserInfo\UpdateUserNickNameDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\User\UserInfo\UpdateUserPhoneDTO;
use App\Facades\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacade;
use App\Facades\LaravelFastApi\V1\Phone\File\PhonePictureFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacadeService
 */
class UserController extends Controller
{
    /**
     * 手机替换
     *
     * @param Request $request
     * @return void
     */


    /**
     * 上传用户头像
     */
    public function uploadUserAvatar(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            if ($request->hasFile('picture')) {
                $picture = $request->file('picture');

                /**
                 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacadeService
                 */
                $result = PhonePictureFacade::uploadUserAvatar($userObject, $picture);
            }
        }

        return $result;
    }

    /**
     * 修改用户昵称
     *
     * @param  Request $request
     */
    public function updateUserNickName(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new UpdateUserNickNameDTO())->validate($request->all());

            //p($validated);die;

            $result = PhoneUserInfoFacade::updateUserNickName($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 修改用户手机号
     *
     * @param  Request $request
     */
    public function updateUserPhone(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new UpdateUserPhoneDTO($userObject))->validate($request->all());

            //p($validated);die;

            $result = PhoneUserInfoFacade::updateUserPhone($requestDTO, $userObject);
        }

        return $result;
    }
}
