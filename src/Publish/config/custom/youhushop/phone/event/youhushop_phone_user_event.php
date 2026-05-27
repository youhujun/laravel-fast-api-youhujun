<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-31 05:21:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-11-05 20:43:41
 * @FilePath: \config\custom\phone\event\youhushop_phone_user_event.php
 */


$goodsCodeArray = [
	'CollectGoods'=>['code'=>10000,'info'=>'收藏商品','event'=>'CollectGoods'],
	'UnCollectGoods'=>['code'=>10000,'info'=>'取消收藏商品','event'=>'UnCollectGoods'],
	'CollectShop'=>['code'=>10000,'info'=>'收藏店铺','event'=>'CollectShop'],
	'UnCollectShop'=>['code'=>10000,'info'=>'取消收藏店铺','event'=>'UnCollectShop'],
];

$orderCodeArray = [
	'UserAddOrder'=>['code'=>10000,'info'=>'用户人添加订单','event'=>'UserAddOrder'],
];

$userCodeArray = [

	'UserApplyOpenShop'=>['code'=>10000,'info'=>'用户申请开店','event'=>'UserApplyOpenShop'],
];

$totalCodeArray = array_merge($userCodeArray,$goodsCodeArray,$orderCodeArray);

return $totalCodeArray;