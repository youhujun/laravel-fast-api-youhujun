<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 08:01:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 21:54:13
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\Order\PayOrderController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Phone\Pay\TestPayOrderDTO;
use App\Facades\LaravelFastApi\V1\Phone\Pay\PhonePayFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacadeService
 */
class PayOrderController extends Controller
{
    /**
    * 获取默认选项
    */
    public function testPayOrder(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $userObject = Auth::guard('phone_token')->user();

        if (Gate::forUser($userObject)->allows('phone-user-role')) {
            $requestDTO = (new TestPayOrderDTO())->validate($request->all());
            $result =  PhonePayFacade::testPayOrder($requestDTO, $userObject);
        }

        return $result;
    }
}
