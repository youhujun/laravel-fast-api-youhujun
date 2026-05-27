<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-31 05:21:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-06 11:27:39
 * @FilePath: \config\custom\phone\code\youhushop_phone_user_code.php
 */


$goodsCodeArray = [
	'ShowGoodsInfoError'=>['code'=>10000,'msg'=>'查看商品详情错误!','error'=>'ShowGoodsInfoError'],
	'CollectGoodsError'=>['code'=>10000,'msg'=>'收藏商品错误!','error'=>'CollectGoodsError'],
	'UnCollectGoodsError'=>['code'=>10000,'msg'=>'取消收藏商品错误!','error'=>'UnCollectGoodsError'],
	'CollectShopError'=>['code'=>10000,'msg'=>'收藏店铺错误!','error'=>'CollectShopError'],
	'UnCollectShopError'=>['code'=>10000,'msg'=>'取消收藏店铺错误!','error'=>'UnCollectShopError'],
];

$orderCodeArray = [

	'UserPayOrderError'=>['code'=>10000,'msg'=>'用户支付订单失败!','error'=>'UserPayOrderError'],
	'UserAmountNoExistError'=>['code'=>10000,'msg'=>'用户账户不存在!','error'=>'UserAmountNoExistError'],
	'UserAmountBalanceNotEnoughError'=>['code'=>10000,'msg'=>'用户余额不足!','error'=>'UserAmountBalanceNotEnoughError'],
	'AddOrderError'=>['code'=>10000,'msg'=>'生成订单失败!','error'=>'AddOrderError'],

	'BindOrderGoodsError'=>['code'=>10000,'msg'=>'绑定订单商品失败!','error'=>'BindOrderGoodsError'],
	'BindOrderGoodsSkuError'=>['code'=>10000,'msg'=>'绑定订单商品sku失败!','error'=>'BindOrderGoodsSkuError'],
	'UserAddressNotExistError'=>['code'=>10000,'msg'=>'用户地址不存在!','error'=>'UserAddressNotExistError'],
	'BindOrderAddressError'=>['code'=>10000,'msg'=>'绑定订单地址失败!','error'=>'BindOrderAddressError'],

];
$userCodeArray = [

	
];



$totalCodeArray = array_merge($userCodeArray,$goodsCodeArray,$orderCodeArray);

return $totalCodeArray;