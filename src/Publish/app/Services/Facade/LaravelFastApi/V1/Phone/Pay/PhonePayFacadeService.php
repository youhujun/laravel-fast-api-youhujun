<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 09:14:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 21:58:37
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Pay;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Phone\CommonException;
use App\DTOs\LaravelFastApi\V1\Phone\Pay\TestPayOrderDTO;
use App\Facades\LaravelFastApi\V1\Phone\Pay\WechatPayFacade;
use App\Models\LaravelFastApi\V1\User\User;
//use UserWechat;需要替换
use App\Models\YouHuShop\V1\Order\Order;
use App\Models\YouHuShop\V1\Order\Union\OrderGoodsUnion;
use App\Models\YouHuShop\V1\Order\Union\OrderGoodsSkuUnion;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Order\PayOrderController
 * @see \App\Facades\LaravelFastApi\V1\Phone\Pay\PhonePayFacade
 */
class PhonePayFacadeService
{
    public function test()
    {
        echo "PhonePayFacadeService test";
    }

    /**
     * 测试支付示例
     *
     * @param  [type] $valdited
     */
    public function testPayOrder(TestPayOrderDTO $requestDTO, User $userObject)
    {
        $result = code(config('phone_code.PayOrderError'));

        if (!isset($validated['pay_type']) || !isset($validated['order_id'])) {
            throw new CommonException('PayOrderParamsError');
        }

        //支付方式
        $pay_type = $requestDTO->pay_type;

        $order_uid = $requestDTO->order_uid;

        $order_real_pay_amount = $requestDTO->real_pay_amount ?? 1;

        //微信的js支付
        if ($pay_type == 10) {
            $result =  WechatPayFacade::payOrderByJsExample($order_uid, $order_real_pay_amount, $userObject);
        }

        return $result;
    }

    /**
     *微信h5支付
     */
    public function payOrderByWechatJs(Order $order, User $userObject)
    {
        $result = code(config('phone_code.PayOrderError'));

        //openid
        $openid = '';

        $where = [];
        $where[] = ['user_uid','=',$userObject->id];
        //openid
        $userWechat = UserWechat::where($where)->first();

        if ($userWechat) {
            $openid =  $userWechat->openid;
        }

        if (!$openid) {
            throw new CommonException('WechatPayOrderByJsNoUserOpenidError');
        }

        $order_id = $order->id;
        $order_sn = $order->order_sn;
        //支付金额
        $order_real_pay_amount = $order->real_pay_amount;

        //订单支付失效时间
        $orderPayExpireTime = time() + 30 * 60;
        //订单描述
        $orderDescription = '购物订单';

        //订单商品数组
        $goodsIdArray = [];

        $orderGoodsUnionCollection = OrderGoodsUnion::where('order_id', $order_id)->get();

        foreach ($orderGoodsUnionCollection as $key => $orderGoodsUnionItem) {
            $goodsIdArray[] = $orderGoodsUnionItem->goods_id;
        }

        $goodsSkuIdArray = [];

        $orderGoodsSkuUnionCollection =  OrderGoodsSkuUnion::where('order_id', $order_id)->get();

        foreach ($orderGoodsSkuUnionCollection as $key => $orderGoodsSkuUnionItem) {
            $goodsSkuIdArray[] = $orderGoodsSkuUnionItem->goods_sku_id;
        }

        //用户id
        $user_uid = $userObject->id;

        //备注
        $bakData = [
            'order' =>
            [
                'order_id' => $order_id,
                'order_sn' => $order_sn,
            ],
            'goodsIdArray' => $goodsIdArray,
            'goodsSkuIdArray' => $goodsSkuIdArray,
            'user' => $user_uid
        ];

        $orderData = [];

        //订单编号
        $orderData['out_trade_no'] = $order_sn;
        //支付金额
        $orderData['amount']['total'] = (int)bcmul($order_real_pay_amount, 100);
        //支付方式
        $orderData['amount']['currency'] = 'CNY';
        //支付失效时间:
        $orderData['time_expire'] = date('Y-m-d\TH:i:sP', $orderPayExpireTime);
        //订单描述
        $orderData['description'] = $orderDescription;
        //openid
        $orderData['payer']['openid'] = $openid;
        //备注信息
        $orderData['attach'] = json_encode($bakData);


        //p($orderData);die;

        $result = WechatPayFacade::payOrderByWechatJs($orderData);

        return $result;
    }
}
