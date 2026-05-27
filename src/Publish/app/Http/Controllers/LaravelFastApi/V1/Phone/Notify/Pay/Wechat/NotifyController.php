<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 10:17:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-08 16:54:24
 * @FilePath: \app\Http\Controllers\Phone\Notify\Pay\Wechat\NotifyController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Notify\Pay\Wechat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Facades\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacadeService
 */
class NotifyController extends Controller
{
    /**
    * 获取默认选项
    */
    public function wechatJsPayNotify(Request $request)
    {
        //p($request->all());die;

        $inWechatpaySignature = $request->header('Wechatpay-Signature');
        $inWechatpayTimestamp = $request->header('Wechatpay-Timestamp');
        $inWechatpaySerial = $request->header('Wechatpay-Serial');
        $inWechatpayNonce = $request->header('Wechatpay-Nonce');
        $inBody = file_get_contents('php://input');

        $notifyData = [
            'wechatpay_signature' => $inWechatpaySignature,
            'wechatpay_timestamp' => $inWechatpayTimestamp,
            'wechatpay_serial' => $inWechatpaySerial,
            'wechatpay_nonce' => $inWechatpayNonce,
            'body' => $inBody
        ];

        //p($notifyData);die;

        /**
         * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacadeService
         */
        $result = PhonePayNotifyFacade::wechatJsPayNotify($notifyData);

        return $result;
    }
}
