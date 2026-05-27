<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-09 15:51:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 15:54:53
 * @FilePath: \youhu-laravel-api-12\config\custom\common\contract\common_contract.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

return [
	'youhu'=>[
		'add_user_handler' => \App\Services\Contract\YouHu\V1\Common\User\YouHuAddUserHandlerContractService::class,
		//订单支付回调
		'order_payment_handler' => \App\Services\Contract\YouHu\V1\Phone\Order\YouHuOrderPaymentHandlerContractService::class,

	],
	'youhushop'=>[
		'delete_goods_class'=>\App\Services\Contract\YouHuShop\V1\Admin\Service\Group\GoodsClass\YouHuShopDeleteGoodsClassContractServiceService::class

	]

];