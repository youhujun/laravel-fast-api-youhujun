<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 09:14:49
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-23 22:56:26
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\Pay;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Phone\CommonException;

use App\Facade\LaravelFastApi\V1\Phone\Pay\WechatPayFacade;

use App\Models\LaravelFastApi\V1\User\User;
//use UserWechat;需要替换
use App\Models\YouHuShop\V1\Order\Order;
use App\Models\YouHuShop\V1\Order\Union\OrderGoodsUnion;
use App\Models\YouHuShop\V1\Order\Union\OrderGoodsSkuUnion;

/**
 * @see \App\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacade
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
   public function testPayOrder($validated,$user)
   {
        $result = code(config('phone_code.PayOrderError'));

        if(!isset($validated['pay_type']) || !isset($validated['order_id']))
        {
            throw new CommonException('PayOrderParamsError');
        }

        //支付方式
        $pay_type = $validated['pay_type'];

        $order_id = $validated['order_id'];

		$order_real_pay_amount = isset($validated['real_pay_amount'])?$validated['real_pay_amount']:1;

        //微信的js支付
        if($pay_type == 10)
        {
           $result =  WechatPayFacade::payOrderByJsExample($order_id,$order_real_pay_amount,$user);
        }

        return $result;
   }

   /**
	*微信h5支付
	*/
   public function payOrderByWechatJs(Order $order,User $user)
   {
		$result = code(config('phone_code.PayOrderError'));

		 //openid
        $openid = '';

        $where = [];
        $where[] = ['user_id','=',$user->id];
        //openid
        $userWechat = UserWechat::where($where)->first();

        if($userWechat )
        {
            $openid =  $userWechat->openid;
        }

        if(!$openid)			
        {
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

		$orderGoodsUnionCollection = OrderGoodsUnion::where('order_id',$order_id)->get();

		foreach ($orderGoodsUnionCollection as $key => $orderGoodsUnionItem) 
		{
			$goodsIdArray[] = $orderGoodsUnionItem->goods_id;
		}

		$goodsSkuIdArray = [];

		$orderGoodsSkuUnionCollection =  OrderGoodsSkuUnion::where('order_id',$order_id)->get();

		foreach ($orderGoodsSkuUnionCollection as $key => $orderGoodsSkuUnionItem) 
		{
			$goodsSkuIdArray[] = $orderGoodsSkuUnionItem->goods_sku_id;
		}

		//用户id
        $user_id = $user->id;

        //备注
        $bakData = [
            'order'=>
            [
                'order_id'=>$order_id,
                'order_sn'=>$order_sn,
            ],
            'goodsIdArray'=>$goodsIdArray,
			'goodsSkuIdArray'=>$goodsSkuIdArray,
            'user'=>$user_id
        ];

		$orderData = [];

        //订单编号
        $orderData['out_trade_no'] = $order_sn;
        //支付金额
        $orderData['amount']['total'] = (int)bcmul($order_real_pay_amount,100);
        //支付方式
        $orderData['amount']['currency'] ='CNY';
        //支付失效时间:
        $orderData['time_expire'] = date('Y-m-d\TH:i:sP',$orderPayExpireTime);
        //订单描述
        $orderData['description'] =$orderDescription;
        //openid
        $orderData['payer']['openid'] =$openid;
        //备注信息
        $orderData['attach'] = json_encode($bakData);


		//p($orderData);die;

		$result = WechatPayFacade::payOrderByWechatJs($orderData);

		return $result;


   }
}
