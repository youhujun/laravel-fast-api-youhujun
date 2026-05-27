<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 17:37:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-15 10:09:52
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\ConfigController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Facades\LaravelFastApi\V1\Phone\User\User\PhoneUserConfigFacade;

class ConfigController extends Controller
{
    /**
    * 获取默认选项
    */
    public function clearUserCache(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $result =  PhoneUserConfigFacade::clearUserCache($userObject);
        }

        return $result;
    }
}
