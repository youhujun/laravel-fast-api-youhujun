<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-10 10:47:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-12 21:36:44
 * @FilePath: \config\custom\admin\code\youhushop_admin_goods_code.php
 */

//商品
$goodsCodeArray = [

	//商品审核
	'AddGoodsApplyCheckLogError'=>['code'=>1000,'msg'=>'添加商品审核记录失败','error'=>'AddGoodsApplyCheckLogError'],
	'GetGoodsApplyCheckError'=>['code'=>1000,'msg'=>'获取商品审核记录失败','error'=>'GetGoodsApplyCheckError'],
	'UnSetGoodsIdError'=>['code'=>1000,'msg'=>'未设置产品id','error'=>'UnSetGoodsIdError'],
	'CheckGoodsError'=>['code'=>1000,'msg'=>'审核商品失败','error'=>'CheckGoodsError'],
	'RefuseInfoIsEmptyError'=>['code'=>1000,'msg'=>'不通过商品原因为空','error'=>'RefuseInfoIsEmptyError'],

	'AddGoodsShareBenefitRateError'=>['code'=>1000,'msg'=>'分润比例参数计算为0','error'=>'AddGoodsShareBenefitRateError'],
	'AddGoodsShareBenefitMoneyError'=>['code'=>1000,'msg'=>'分润金额计算为0','error'=>'AddGoodsShareBenefitMoneyError'],
	'AddGoodsOnSaleTimeError'=>['code'=>1000,'msg'=>'上架时间设置错误!','error'=>'AddGoodsOnSaleTimeError'],

	//获取商品列表
	'GetGoodsError'=>['code'=>10000,'msg'=>'获取商品列表成功!','error'=>'GetGoodsError'],
	'GetSingleGoodsByIdError'=>['code'=>10000,'msg'=>'获取单个商品成功!','error'=>'GetSingleGoodsByIdError'],

	//添加商品
	'AddGoodsError'=>['code'=>10000,'msg'=>'添加商品失败!','error'=>'AddGoodsError'],

	//请求参数
	'ParamsGoodsTypeIsNullError'=>['code'=>10000,'msg'=>'商品类型为空!','error'=>'ParamsGoodsTypeIsNullError'],
	'ParamsGoodsClassIdIsNullError'=>['code'=>10000,'msg'=>'商品分类为空!','error'=>'ParamsGoodsClassIdIsNullError'],
	'ParamsGoodsClassIdIsArrayNullError'=>['code'=>10000,'msg'=>'商品分类参数为空!','error'=>'ParamsGoodsClassIdIsArrayNullError'],
	'ParamsGoodsNameIsNullError'=>['code'=>10000,'msg'=>'商品名称为空!','error'=>'ParamsGoodsNameIsNullError'],
	'ParamsGoodsMainPcitureIdIsNullError'=>['code'=>10000,'msg'=>'商品主图为空!','error'=>'ParamsGoodsMainPcitureIdIsNullError'],
	'ParamsSalePriceIsNullError'=>['code'=>10000,'msg'=>'商品售价为空!','error'=>'ParamsSalePriceIsNullError'],
	'ParamsShareBenefitTypeIsNullError'=>['code'=>10000,'msg'=>'商品分润类型为空!','error'=>'ParamsShareBenefitTypeIsNullError'],
	'ParamsStoreNumberIsNullError'=>['code'=>10000,'msg'=>'商品库存为空!','error'=>'ParamsStoreNumberIsNullError'],
	'ParamsSystemSpecIsNullError'=>['code'=>10000,'msg'=>'商品系统规格为空!','error'=>'ParamsSystemSpecIsNullError'],
	'ParamsCustomSpecIsNullError'=>['code'=>10000,'msg'=>'商品自定义规格为空!','error'=>'ParamsCustomSpecIsNullError'],
	'ParamsSystemAttributeIsNullError'=>['code'=>10000,'msg'=>'商品系统属性为空!','error'=>'ParamsSystemAttributeIsNullError'],
	'ParamsCustomAttributeIsNullError'=>['code'=>10000,'msg'=>'商品自定义属性为空!','error'=>'ParamsCustomAttributeIsNullError'],
	'ParamsServerRegionTypeIsNullError'=>['code'=>10000,'msg'=>'商品服务类型为空!','error'=>'ParamsServerRegionTypeIsNullError'],
	'ParamsExpenseTypeIsNullError'=>['code'=>10000,'msg'=>'商品运费类型为空!','error'=>'ParamsExpenseTypeIsNullError'],
	'ParamsSendRegionTypeIsNullError'=>['code'=>10000,'msg'=>'商品发货地区类型为空!','error'=>'ParamsSendRegionTypeIsNullError'],
	'ParamsSendRegionArrayIsNullError'=>['code'=>10000,'msg'=>'商品发货地区参数为空!','error'=>'ParamsSendRegionArrayIsNullError'],
	'ParamsSendAddressInfoIsNullError'=>['code'=>10000,'msg'=>'商品发货地址详情为空!','error'=>'ParamsSendAddressInfoIsNullError'],
	'ParamsGoodsInfoIsNullError'=>['code'=>10000,'msg'=>'商品详情为空!','error'=>'ParamsGoodsInfoIsNullError'],
	'ParamsSortIsNullError'=>['code'=>10000,'msg'=>'商品排序为空!','error'=>'ParamsSortIsNullError'],

	'AddGoodsHaveUnfinishCountError'=>['code'=>1000,'msg'=>'未审核的商品数量已经达到3个!','error'=>'AddGoodsHaveUnfinishCountError'],
	'AddGoodsPriceError'=>['code'=>1000,'msg'=>'商品价格设置错误!','error'=>'AddGoodsPriceError'],
	'AddGoodsDecutCoinError'=>['code'=>1000,'msg'=>'商品抵扣游鹄币设置过大!','error'=>'AddGoodsDecutCoinError'],
	'AddGoodsStoreNumberError'=>['code'=>1000,'msg'=>'商品库存设置错误!','error'=>'AddGoodsStoreNumberError'],

	//添加商品事件
	'BindGoodsClassError'=>['code'=>10000,'msg'=>'绑定商品分类失败!','error'=>'BindGoodsClassError'],
	'BindGoodsInfoError'=>['code'=>10000,'msg'=>'绑定商品详情失败!','error'=>'BindGoodsInfoError'],
	'BindGoodsMainPictureError'=>['code'=>10000,'msg'=>'绑定商品主图失败!','error'=>'BindGoodsMainPictureError'],
	'BindGoodsBrandError'=>['code'=>10000,'msg'=>'绑定商品品牌!','error'=>'BindGoodsBrandError'],

	'GoodsSkuSalePriceError'=>['code'=>10000,'msg'=>'商品sku售价错误!','error'=>'GoodsSkuSalePriceError'],
	'GoodsSkuStoreNumberError'=>['code'=>10000,'msg'=>'商品sku库存错误!','error'=>'GoodsSkuStoreNumberError'],
	'GoodsSkuSpecError'=>['code'=>10000,'msg'=>'商品sku规格错误!','error'=>'GoodsSkuSpecError'],
	'GoodsSkuPictureError'=>['code'=>10000,'msg'=>'商品sku图片不能为空!','error'=>'GoodsSkuPictureError'],
	'AddGoodsSkuError'=>['code'=>10000,'msg'=>'添加商品sku错误!','error'=>'AddGoodsSkuError'],
	'CheckGoodsTotalStoreNumberError'=>['code'=>10000,'msg'=>'检测商品库存错误!','error'=>'CheckGoodsTotalStoreNumberError'],
	'BindGoodsSkuSpecValueUnionError'=>['code'=>10000,'msg'=>'绑定sku商品规格尺寸错误!','error'=>'BindGoodsSkuSpecValueUnionError'],
	'BindGoodsSkuPictureUnionError'=>['code'=>10000,'msg'=>'绑定sku商品图片错误!','error'=>'BindGoodsSkuPictureUnionError'],

	'BindGoodsSpecNameError'=>['code'=>10000,'msg'=>'绑定商品规格名错误!','error'=>'BindGoodsSpecNameError'],
	'BindGoodsSpecNameValueError'=>['code'=>10000,'msg'=>'绑定商品属性值错误!','error'=>'BindGoodsSpecNameValueError'],

	'BindGoodsAttributeNameError'=>['code'=>10000,'msg'=>'绑定商品属性名错误!','error'=>'BindGoodsSpecNameValueError'],
	'BindGoodsAttributeNameValueError'=>['code'=>10000,'msg'=>'绑定商品属性值错误!','error'=>'BindGoodsSpecNameValueError'],



	//更新商品
	'UpdateGoodsError'=>['code'=>10000,'msg'=>'修改商品失败!','error'=>'UpdateGoodsError'],
	'UpdateGoodsPropertyError'=>['code'=>10000,'msg'=>'获取修改商品属性失败!','error'=>'UpdateGoodsPropertyError'],
	'DeleteGoodsError'=>['code'=>10000,'msg'=>'删除商品失败!','error'=>'DeleteGoodsError'],
	'RestoreGoodsError'=>['code'=>10000,'msg'=>'恢复商品失败!','error'=>'RestoreGoodsError'],
	'MultipleDeleteGoodsError'=>['code'=>10000,'msg'=>'批量删除商品失败!','error'=>'DeleteGoodsError'],
	'MultipleRestoreGoodsError'=>['code'=>10000,'msg'=>'批量恢复商品失败!','error'=>'MultipleRestoreGoodsError'],
	'ThisDataPropertyNoAllowError'=>['code'=>10000,'msg'=>'不允许修改该属性!','error'=>'ThisDataPropertyNoAllowError'],
];

//商品规格
$goodsSpecCodeArray = [
	'GetGoodsSpecNameError'=>['code'=>1000,'msg'=>'获取商品规格名列表失败','error'=>'GetGoodsSpecNameError'],
	'AddGoodsSpecNameError'=>['code'=>1000,'msg'=>'添加商品规格名失败','error'=>'AddGoodsSpecNameError'],
	'UpdateGoodsSpecNameError'=>['code'=>1000,'msg'=>'更新商品规格名失败','error'=>'UpdateGoodsSpecNameError'],
	'DeleteGoodsSpecNameError'=>['code'=>1000,'msg'=>'删除商品规格名失败','error'=>'DeleteGoodsSpecNameError'],
	'RestoreGoodsSpecNameError'=>['code'=>1000,'msg'=>'恢复商品规格名失败','error'=>'RestoreGoodsSpecNameError'],
	'MultipleDeleteGoodsSpecNameError'=>['code'=>1000,'msg'=>'批量删除商品规格名失败','error'=>'MultipleDeleteGoodsSpecNameError'],
	'MultipleRestoreGoodsSpecNameError'=>['code'=>1000,'msg'=>'批量恢复商品规格名失败','error'=>'MultipleRestoreGoodsSpecNameError'],
	'UpdateGoodsSpecNamePropertyError'=>['code'=>1000,'msg'=>'更新商品规格名单个属性失败','error'=>'UpdateGoodsSpecNamePropertyError'],

	//选项
	'DefaultGoodsSpecNameError'=>['code'=>1000,'msg'=>'获取默认规格名选项失败','error'=>'DefaultGoodsSpecNameError'],
	'FindGoodsSpecNameError'=>['code'=>1000,'msg'=>'查找默认规格名选项失败','error'=>'FindGoodsSpecNameError'],

	//商品分类和商品规格名
	'BindGoodsSpecNameError'=>['code'=>1000,'msg'=>'绑定商品分类和商品规格名失败','error'=>'FindGoodsSpecNameError'],
	'GetGoodsClassBindSpecNameError'=>['code'=>1000,'msg'=>'获取商品分类规格名失败','error'=>'GetGoodsClassBindSpecNameError'],
	'GetAllGoodsClassBindSpecNameError'=>['code'=>1000,'msg'=>'获取所有商品分类规格名失败','error'=>'GetAllGoodsClassBindSpecNameError'],
	'RemoveGoodsSpecNameError'=>['code'=>1000,'msg'=>'移除商品分类和商品规格名失败','error'=>'RemoveGoodsSpecNameError'],
	'MultipleRemoveGoodsSpecNameError'=>['code'=>1000,'msg'=>'批量移除商品分类和商品规格名失败','error'=>'MultipleRemoveGoodsSpecNameError'],

	'AddCustomGoosSpecNameError'=>['code'=>1000,'msg'=>'添加自动定义商品规格名失败','error'=>'AddCustomGoosSpecNameError'],

	'AddGoosSpecValueError'=>['code'=>1000,'msg'=>'添加商品规格值失败','error'=>'AddGoosSpecValueError'],

	'DefaultGoodsSpecValueError'=>['code'=>1000,'msg'=>'获取默认商品规格值失败','error'=>'DefaultGoodsSpecValueError'],

	'FindGoodsSpecValueError'=>['code'=>1000,'msg'=>'查找默认商品规格值失败','error'=>'FindGoodsSpecValueError'],
];

//商品属性
$goodsAttributeCodeArray = [
	'GetGoodsAttributeNameError'=>['code'=>1000,'msg'=>'获取商品属性名列表失败','error'=>'GetGoodsAttributeNameError'],
	'AddGoodsAttributeNameError'=>['code'=>1000,'msg'=>'添加商品属性名失败','error'=>'AddGoodsAttributeNameError'],
	'UpdateGoodsAttributeNameError'=>['code'=>1000,'msg'=>'更新商品属性名失败','error'=>'UpdateGoodsAttributeNameError'],
	'DeleteGoodsAttributeNameError'=>['code'=>1000,'msg'=>'删除商品属性名失败','error'=>'DeleteGoodsAttributeNameError'],
	'RestoreGoodsAttributeNameError'=>['code'=>1000,'msg'=>'恢复商品属性名失败','error'=>'RestoreGoodsAttributeNameError'],
	'MultipleDeleteGoodsAttributeNameError'=>['code'=>1000,'msg'=>'批量删除商品属性名失败','error'=>'MultipleDeleteGoodsAttributeNameError'],
	'MultipleRestoreGoodsAttributeNameError'=>['code'=>1000,'msg'=>'批量恢复商品属性名失败','error'=>'MultipleRestoreGoodsAttributeNameError'],
	'UpdateGoodsAttributeNamePropertyError'=>['code'=>1000,'msg'=>'更新商品属性名单个属性失败','error'=>'UpdateGoodsAttributeNamePropertyError'],

	//选项
	'DefaultGoodsAttributeNameError'=>['code'=>1000,'msg'=>'获取默认属性名选项失败','error'=>'DefaultGoodsAttributeNameError'],
	'FindGoodsAttributeNameError'=>['code'=>1000,'msg'=>'查找默认属性名选项失败','error'=>'FindGoodsAttributeNameError'],

	//商品分类和商品属性名
	'BindGoodsAttributeNameError'=>['code'=>1000,'msg'=>'绑定商品分类和商品属性名失败','error'=>'FindGoodsAttributeNameError'],
	'GetGoodsClassBindAttributeNameError'=>['code'=>1000,'msg'=>'获取商品分类属性名失败','error'=>'GetGoodsClassBindAttributeNameError'],
	'GetAllGoodsClassBindAttributeNameError'=>['code'=>1000,'msg'=>'获取所有商品分类属性名失败','error'=>'GetAllGoodsClassBindAttributeNameError'],
	'RemoveGoodsAttributeNameError'=>['code'=>1000,'msg'=>'移除商品分类和商品属性名失败','error'=>'RemoveGoodsAttributeNameError'],
	'MultipleRemoveGoodsAttributeNameError'=>['code'=>1000,'msg'=>'批量移除商品分类和商品属性名失败','error'=>'MultipleRemoveGoodsAttributeNameError'],

	'AddCustomGoosAttributeNameError'=>['code'=>1000,'msg'=>'添加自动定义商品属性名失败','error'=>'AddCustomGoosAttributeNameError'],

	'AddGoosAttributeValueError'=>['code'=>1000,'msg'=>'添加商品属性值失败','error'=>'AddGoosAttributeValueError'],

	'DefaultGoodsAttributeValueError'=>['code'=>1000,'msg'=>'获取默认商品属性值失败','error'=>'DefaultGoodsAttributeValueError'],

	'FindGoodsAttributeValueError'=>['code'=>1000,'msg'=>'查找默认商品属性值失败','error'=>'FindGoodsAttributeValueError'],
];

//商品品牌
$goodsBrandCodeArray = [
	//选项
	//默认商品品牌选项
	'DefaultGoodsBrandError'=>['code'=>10000,'msg'=>'获取默认商品品牌选项成功!','error'=>'DefaultGoodsBrandError'],
	 //查找商品品牌选项
	'FindGoodsBrandError'=>['code'=>10000,'msg'=>'获取默认商品品牌选项成功!','error'=>'FindGoodsBrandError'],

	//获取商品品牌列表
	'GetGoodsBrandError'=>['code'=>10000,'msg'=>'获取默认商品品牌列表成功!','error'=>'GetGoodsBrandError'],
	'AddGoodsBrandError'=>['code'=>10000,'msg'=>'添加商品品牌成功!','error'=>'AddGoodsBrandError'],
	'UpdateGoodsBrandError'=>['code'=>10000,'msg'=>'修改商品品牌成功!','error'=>'UpdateGoodsBrandError'],
	'UpdateGoodsBrandPropertyError'=>['code'=>10000,'msg'=>'获取修改商品品牌属性成功!','error'=>'UpdateGoodsBrandPropertyError'],
	'DeleteGoodsBrandError'=>['code'=>10000,'msg'=>'删除商品品牌成功!','error'=>'DeleteGoodsBrandError'],
	'RestoreGoodsBrandError'=>['code'=>10000,'msg'=>'恢复商品品牌成功!','error'=>'RestoreGoodsBrandError'],
	'MultipleDeleteGoodsBrandError'=>['code'=>10000,'msg'=>'批量删除商品品牌成功!','error'=>'DeleteGoodsBrandError'],
	'MultipleRestoreGoodsBrandError'=>['code'=>10000,'msg'=>'批量恢复商品品牌成功!','error'=>'MultipleRestoreGoodsBrandError'],
];

$totalCodeArray = array_merge($goodsCodeArray,$goodsSpecCodeArray,$goodsAttributeCodeArray,$goodsBrandCodeArray);

return $totalCodeArray;