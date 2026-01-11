<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 12:50:24
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 23:04:47
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\Notify;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Facade\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacade;
use App\Contract\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract;

/**
 * @see \App\Facade\Phone\Notify\PhonePayNotifyFacade
 */
class PhonePayNotifyFacadeService
{
    public function test()
    {
        echo "PhonePayNotifyFacadeService test";
    }

    private $paymentHandler;

    public function __construct(OrderPaymentHandlerContract $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    /**
     *
     *
     * @param  [type] $notifyData
     */
    public function wechatJsPayNotify($notifyData)
    {
        //p($notifyData);die;
        $result = code(config('phone_code.WechatJsPayNotifyError'));

        plog(['$notifyData:微信Js支付回调数据' => $notifyData], 'wechatJsPayNotify', 'wechatJsPayNotify');

        // '$notifyData:微信Js支付回调数据' =>
        // array (
        // 	'$inWechatpaySignature' => 'Ce+Zdhn2NJHSb5iSNZxoE2GwQ3e0FeZxYF4YjaQBgmgH9lSyyWD5MRUlmirfF9kvMwVB9VN83qbuKp1uHLtMsS5mOeCiLkbYuuSTQY4P9AWqqEi6xCPXvDSll/FV7IeYFHvcyAx5zygdKPg/kxK6vH8CLQeGN+7unwMj+cu27OGnmD01VTj6bnIPnhBo8Mu6cAeKYpNB7tjMfpdXsomCR2MwOmPXTlksNY7pkRK5rUT50474lHtqImxZqfBpWL0YLqgBqwzgn8Qvt4awsk1EvWqUXE1xisPadrz9NwyuD8aY9nNRnIrdg1BMPlQO6+NGXnwlrJqr+mMkSPem/ZqSZw==',
        // 	'$inWechatpayTimestamp' => '1728395521',
        // 	'$inWechatpaySerial' => '4D692D0FAE97194D3A29BD0F398B4544E9AC903D',
        // 	'$inWechatpayNonce' => 'yee40qXRhPKINj69AwYeQPfzZHarpnHZ',
        // 	'$inBody' => '{"id":"732fbfb3-fd52-56d3-b53f-e543cf08b2b2","create_time":"2024-10-08T18:47:38+08:00","resource_type":"encrypt-resource","event_type":"TRANSACTION.SUCCESS","summary":"支付成功","resource":{"original_type":"transaction","algorithm":"AEAD_AES_256_GCM","ciphertext":"u4ziMu1cLRsK1tvGdUrCLL1OMYsO8DzGzxpbHF2IsTmQnzgKva6La11lD8WLY62jrb0grGR9sZCdK00vK/uskZDEHWExoyGsv2R1cxIMj9mZtKItAnWE1cbZeJ6UhVgNfdTtz3RL7dNxWzwXy1+KpCTb94ikBnQoD+LXR96Wc19p9rsjWYNlexEdecfnW2L3Ek7upNbBiKOY/8PRXN8u9kx6KK9AiAALELf2/gvmTZtk0ZxR+lR/h8zktRMX1/0r98pzetgji2K0p/GyJQ+0HFoTCn4uJoYYT1p3LpSVSwiha/TMrEv3+NLaOUy/2CL8MfWJMxQAQWs1/3Oi9CtUccn/1J7iRfZfaIHEv5E1b0sBAr8e1jGq/wYrW3tBD3xFXA11Z6SPgnFrwUW8Ybb6CS4/SMjD5buE8MaXHfZT86BJepiIBi7iVkAH+AwTBKZgdEk1mQUr88AToQufBQ/FFDSlRHREBOWDrqo7ToXnZoYFuAShCLcDy+DERmhz5Szv9TJ2PB9X6Xw3o800XOAQ5i7OhYKxbifASspnez6P1y02yX4LSJJrlGgWVVUdI+GcPrW+4i3hiv0MZWyBo27ax9/V10xvAUP1ZtmJC68YjSP7VzoxVLb+W4976KvyMcmcRs6RZf1P5DIuZB8YF83PQi2DoGkxdstbBBZR+FC7vz4rGz95ilQ888krgymKBjTR50rfS8CbOSo0XbDwiesLcqz6sMksjMPNvw==","associated_data":"transaction","nonce":"LkAZkgBNkzQW"}}',
        // ),

        $decryptedData = WechatJsPayDecryptFacade::decryptData($notifyData);

        plog(['$decryptedData:解密数据2' => $decryptedData], 'wechatJsPayNotify', 'wechatJsPayDecryptedData');

        // '$decryptedData:解密数据' =>
        // array (
        // 	'mchid' => '1634317589',
        // 	'appid' => 'wx09f89f9e9d5f72ba',
        // 	'out_trade_no' => '2024100851991024',
        // 	'transaction_id' => '4200002332202410082732365633',
        // 	'trade_type' => 'JSAPI',
        // 	'trade_state' => 'SUCCESS',
        // 	'trade_state_desc' => '支付成功',
        // 	'bank_type' => 'OTHERS',
        // 	'attach' => '{"order":{"order_id":50,"order_sn":"2024100851991024"},"goodsIdArray":[1],"goodsSkuIdArray":[],"user":1}',
        // 	'success_time' => '2024-10-08T18:47:38+08:00',
        // 	'payer' =>
        // 	array (
        // 	'openid' => 'oYFqa5nW_bn2icDWNBi4xEXpRf5E',
        // 	),
        // 	'amount' =>
        // 	array (
        // 	'total' => 100,
        // 	'payer_total' => 100,
        // 	'currency' => 'CNY',
        // 	'payer_currency' => 'CNY',
        // 	),
        // ),

        // 处理支付成功逻辑

        ['transaction_id' => $transaction_id,'trade_type' => $trade_type,'trade_state' => $trade_state,'bank_type' => $bank_type,'attach' => $attach,'amount' => $amount] = $decryptedData + [];

        if ($trade_state === 'SUCCESS') {
            //支付订单号
            $attachArray = \json_decode($attach, true);

            //防止报错,先给默认值
            $defaultAttachArray = ['order' => null, 'goodsIdArray' => null,'user' => null];

            $mergedAttachArray = array_merge($defaultAttachArray, $attachArray);

            ['order' => $orderArray,'goodsIdArray' => $goodsIdArray,'user' => $user_id] = $mergedAttachArray + [];

            //防止报错,先给默认值
            $defaultOrderArray = ['order_id' => null,'order_sn' => null];

            $mergedOrderArray = array_merge($defaultOrderArray, $orderArray);

            ['order_id' => $order_id,'order_sn' => $order_sn] = $mergedOrderArray + [];

            //防止报错,先给默认值
            $defaultAmountArray = ['total' => null,'payer_total' => null];

            $mergeAmountArray = array_merge($defaultAmountArray, $amount);

            ['total' => $total,'payer_total' => $payer_total] = $mergeAmountArray + [];

            $handleParam = ['order_id' => $order_id, 'transaction_id' => $transaction_id, 'user_id' => $user_id, 'payer_total' => $payer_total];
            //执行支付回调
            app(OrderPaymentHandlerContract::class)->handle($handleParam);


            $result = ['code' => 'SUCCESS','message' => '支付成功!'];
        } else {
            $result = ['code' => 'FAIL','message' => '支付失败!'];
        }

        return $result;
    }
}
