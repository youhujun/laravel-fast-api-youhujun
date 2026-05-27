<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-10 10:49:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-09 18:02:58
 * @FilePath: \config\custom\youhushop\admin\event\youhushop_admin_goods_event.php
 */

 //商品规格
$goodsSpecCodeArray = [
	
	'AddGoodsSpecName'=> [ 'code' => 10000, 'info' => '添加产品规格名!','event'=>'AddGoodsSpecName'],
	'UpdateGoodsSpecName'=> [ 'code' => 10000, 'info' => '更新产品规格名!','event'=>'UpdateGoodsSpecName'],
	'DeleteGoodsSpecName'=> [ 'code' => 10000, 'info' => '删除产品规格名!','event'=>'DeleteGoodsSpecName'],
	'RestoreGoodsSpecName'=> [ 'code' => 10000, 'info' => '恢复产品规格名!','event'=>'RestoreGoodsSpecName'],
	'MultipleDeleteGoodsSpecName'=> [ 'code' => 10000, 'info' => '批量删除产品规格名!','event'=>'MultipleDeleteGoodsSpecName'],
	'MultipleRestoreGoodsSpecName'=> [ 'code' => 10000, 'info' => '批量恢复产品规格名!','event'=>'MultipleRestoreGoodsSpecName'],
	'UpdateGoodsSpecNameProperty'=> [ 'code' => 10000, 'info' => '更新产品规格名单个属性!','event'=>'UpdateGoodsSpecNameProperty'],
	//产品分类和产品规格名
	'BindGoodsSpecName'=> [ 'code' => 10000, 'info' => '绑定产品分类和产品规格名!','event'=>'BindGoodsSpecName'],

	'RemoveGoodsSpecName'=> [ 'code' => 10000, 'info' => '移除产品分类和产品规格名!','event'=>'RemoveGoodsSpecName'],

	'MultipleRemoveGoodsSpecName'=> [ 'code' => 10000, 'info' => '批量移除产品分类和产品规格名!','event'=>'MultipleRemoveGoodsSpecName'],

	'AddCustomGoosSpecName'=> [ 'code' => 10000, 'info' => '添加自定义产品规格名!','event'=>'AddCustomGoosSpecName'],

	'AddCustomGoosSpecName'=> [ 'code' => 10000, 'info' => '添加自定义产品规格名!','event'=>'AddCustomGoosSpecName'],

	'AddGoodsSpecValue'=> [ 'code' => 10000, 'info' => '添加产品规格值!','event'=>'AddGoodsSpecValue'],
	'UpdateGoodsSpecValue'=> [ 'code' => 10000, 'info' => '更新产品规格值!','event'=>'UpdateGoodsSpecValue'],
	'DeleteGoodsSpecValue'=> [ 'code' => 10000, 'info' => '删除产品规格值!','event'=>'DeleteGoodsSpecValue'],
	'MultipleDeleteGoodsSpecValue'=> [ 'code' => 10000, 'info' => '批量删除产品规格值!','event'=>'MultipleDeleteGoodsSpecValue'],

];

//商品属性
$goodsArrtibuteCodeArray = [

	'AddGoodsAttributeName'=> [ 'code' => 10000, 'info' => '添加产品属性名!','event'=>'AddGoodsAttributeName'],
	'UpdateGoodsAttributeName'=> [ 'code' => 10000, 'info' => '更新产品属性名!','event'=>'UpdateGoodsAttributeName'],
	'DeleteGoodsAttributeName'=> [ 'code' => 10000, 'info' => '删除产品属性名!','event'=>'DeleteGoodsAttributeName'],
	'RestoreGoodsAttributeName'=> [ 'code' => 10000, 'info' => '恢复产品属性名!','event'=>'RestoreGoodsAttributeName'],
	'MultipleDeleteGoodsAttributeName'=> [ 'code' => 10000, 'info' => '批量删除产品属性名!','event'=>'MultipleDeleteGoodsAttributeName'],
	'MultipleRestoreGoodsAttributeName'=> [ 'code' => 10000, 'info' => '批量恢复产品属性名!','event'=>'MultipleRestoreGoodsAttributeName'],
	'UpdateGoodsAttributeNameProperty'=> [ 'code' => 10000, 'info' => '更新产品属性名单个属性!','event'=>'UpdateGoodsAttributeNameProperty'],
	//产品分类和产品属性名
	'BindGoodsAttributeName'=> [ 'code' => 10000, 'info' => '绑定产品分类和产品属性名!','event'=>'BindGoodsAttributeName'],

	'RemoveGoodsAttributeName'=> [ 'code' => 10000, 'info' => '移除产品分类和产品属性名!','event'=>'RemoveGoodsAttributeName'],

	'MultipleRemoveGoodsAttributeName'=> [ 'code' => 10000, 'info' => '批量移除产品分类和产品属性名!','event'=>'MultipleRemoveGoodsAttributeName'],

	'AddCustomGoosAttributeName'=> [ 'code' => 10000, 'info' => '添加自定义产品属性名!','event'=>'AddCustomGoosAttributeName'],

	'AddGoodsAttributeValue'=> [ 'code' => 10000, 'info' => '添加产品属性值!','event'=>'AddGoodsAttributeValue'],
	'UpdateGoodsAttributeValue'=> [ 'code' => 10000, 'info' => '更新产品属性值!','event'=>'UpdateGoodsAttributeValue'],
	'DeleteGoodsAttributeValue'=> [ 'code' => 10000, 'info' => '删除产品属性值!','event'=>'DeleteGoodsAttributeValue'],
	'MultipleDeleteGoodsAttributeValue'=> [ 'code' => 10000, 'info' => '批量删除产品属性值!','event'=>'MultipleDeleteGoodsAttributeValue'],
];

//商品品牌
 $goodsBrandCodeArray = [
	'AddGoodsBrand' =>  [ 'code' => 10000, 'info' => '添加商品品牌','event'=>'AddGoodsBrand' ],
	'UpdateGoodsBrand' =>  [ 'code' => 10000, 'info' => '修改商品品牌','event'=>'UpdateGoodsBrand' ],
	'UpdateGoodsBrandProperty' =>  [ 'code' => 10000, 'info' => '修改商品品牌属性','event'=>'UpdateGoodsBrandProperty' ],
	'DeleteGoodsBrand' =>  [ 'code' => 10000, 'info' => '删除商品品牌','event'=>'DeleteGoodsBrand' ],
	'RestoreGoodsBrand' =>  [ 'code' => 10000, 'info' => '恢复商品品牌','event'=>'RestoreGoodsBrand' ],
	'MultipleDeleteGoodsBrand' =>  [ 'code' => 10000, 'info' => '批量删除商品品牌','event'=>'MultipleDeleteGoodsBrand' ],
	'MultipleRestoreGoodsBrand' =>  [ 'code' => 10000, 'info' => '批量恢复商品品牌','event'=>'MultipleRestoreGoodsBrand' ],
];

//商品
$goodsCodeArray = [
	'AddGoods' =>  [ 'code' => 10000, 'info' => '添加商品','event'=>'AddGoods' ],
	'UpdateGoods' =>  [ 'code' => 10000, 'info' => '修改商品','event'=>'UpdateGoods' ],
	'UpdateGoodsProperty' =>  [ 'code' => 10000, 'info' => '修改商品属性','event'=>'UpdateGoodsProperty' ],
	'DeleteGoods' =>  [ 'code' => 10000, 'info' => '删除商品','event'=>'DeleteGoods' ],
	'RestoreGoods' =>  [ 'code' => 10000, 'info' => '恢复商品','event'=>'RestoreGoods' ],
	'MultipleDeleteGoods' =>  [ 'code' => 10000, 'info' => '批量删除商品','event'=>'MultipleDeleteGoods' ],
	'MultipleRestoreGoods' =>  [ 'code' => 10000, 'info' => '批量恢复商品','event'=>'MultipleRestoreGoods' ],
	'CheckGoods' =>  [ 'code' => 10000, 'info' => '审核商品','event'=>'CheckGoods' ],
];

$totalCodeArray = array_merge($goodsSpecCodeArray,$goodsArrtibuteCodeArray,$goodsBrandCodeArray,$goodsCodeArray);

return $totalCodeArray;