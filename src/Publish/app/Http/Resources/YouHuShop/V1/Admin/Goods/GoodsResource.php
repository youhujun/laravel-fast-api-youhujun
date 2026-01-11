<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-26 18:34:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-23 22:50:47
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\GoodsResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClass;
use App\Models\LaravelFastApi\V1\System\Module\Goods\GoodsClassUnion;

use App\Models\YouHuShop\V1\Goods\Union\Brand\GoodsBrandUnion;
use App\Models\YouHuShop\V1\Goods\GoodsBrand;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\YouHuShop\V1\Goods\Union\Picture\GoodsMainPictureUnion;

use App\Models\YouHuShop\V1\Goods\Union\Spec\GoodsSpecNameUnion;
use App\Models\YouHuShop\V1\Goods\Union\Spec\GoodsSpecNameValueUnion;

use App\Models\YouHuShop\V1\Goods\Spec\GoodsSpecName;
use App\Models\YouHuShop\V1\Goods\Spec\GoodsSpecValue;

use App\Models\YouHuShop\V1\Goods\Attribute\GoodsAttributeName;
use App\Models\YouHuShop\V1\Goods\Attribute\GoodsAttributeValue;

use App\Models\YouHuShop\V1\Goods\Union\Attribute\GoodsAttributeNameUnion;
use App\Models\YouHuShop\V1\Goods\Union\Attribute\GoodsAttributeNameValueUnion;

use App\Models\YouHuShop\V1\Goods\GoodsSku;
use App\Models\YouHuShop\V1\Goods\Union\Sku\GoodsSkuSpecValueUnion;
use App\Models\YouHuShop\V1\Goods\Union\Sku\GoodsSkuPictureUnion;

use App\Models\YouHuShop\V1\Goods\Property\Region\GoodsSendAddress;

use GoodsDescription as GoodsInfo;



class GoodsResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

    public static function setReplaceType($replaceType = 10)
    {
        self::$replaceType = $replaceType;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

       $response = [];

        if(\is_array($this->resource))
        {
             $response = [
                'id'=>$this->resource['id'],
                
            ];

           
        }

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'owner_type'=>$this->resource->owner_type,
                'admin_id'=>$this->resource->admin_id,
                'server_id'=>$this->resource->server_id,
                'shop_id'=>$this->resource->shop_id,
                'on_sale_type'=>$this->resource->on_sale_type,
                'is_on_sale'=>$this->resource->is_on_sale,
                'check_status'=>$this->resource->check_status,
                'auth_status'=>$this->resource->auth_status,
                'goods_type'=>$this->resource->goods_type,
                'serve_region_type'=>$this->resource->serve_region_type,
                'expense_type'=>$this->resource->expense_type,
                'send_region_type'=>$this->resource->send_region_type,
                'is_top'=>$this->resource->is_top,
                'is_recommend'=>$this->resource->is_recommend,
                'is_new'=>$this->resource->is_new,
                'is_discount'=>$this->resource->is_discount,
                'goods_name'=>$this->resource->goods_name,
                'goods_introduction'=>$this->resource->goods_introduction,
                'goods_code'=>$this->resource->goods_code,
                'goods_line_code'=>$this->resource->goods_line_code,
                'goods_spu'=>$this->resource->goods_spu,
                'sale_price'=>number_format($this->resource->sale_price,2,'.',''),
                'market_price'=>number_format($this->resource->market_price,2,'.',''),
                'settle_price'=>number_format($this->resource->settle_price,2,'.',''),
                'cost_price'=>number_format($this->resource->cost_price,2,'.',''),
                'coin'=>number_format($this->resource->coin,2,'.',''),
                'deduct_coin'=>number_format($this->resource->deduct_coin,2,'.',''),
                'goods_star'=>$this->resource->goods_star,
                'share_benefit_type'=>$this->resource->share_benefit_type,
                'share_benefit_money'=>number_format($this->resource->share_benefit_money,2,'.',''),
                'share_benefit_rate'=>bcmul($this->resource->share_benefit_rate,100),
                'store_number'=>$this->resource->store_number,
                'store_warning_number'=>$this->resource->store_warning_number,
                'duration'=>$this->resource->duration,
                'on_sale_at'=>$this->resource->on_sale_at,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];

			//处理产品分类
			$goodsClassUnionObject = GoodsClassUnion::where('goods_id',$this->resource->id)->first();

			if($goodsClassUnionObject)
			{
				$response['goods_class_id'] = $goodsClassUnionObject->goods_class_id;
				$response['goodsClassArray'] = [$goodsClassUnionObject->goods_class_one_depp_id,$goodsClassUnionObject->goods_class_two_depp_id,$goodsClassUnionObject->goods_class_three_depp_id];

				$goods_class = GoodsClass::where('id',$goodsClassUnionObject->goods_class_id)->first();

				if($goods_class)
				{
					$response['goods_class_name'] = $goods_class->goods_class_name;
				}
			}

			$response['goods_brand_id'] = null;
			$response['goods_brand_name'] = '';

			//处理品牌
			$gooodsBrandUnionObject = GoodsBrandUnion::where('goods_id',$this->resource->id)->first();

			if($gooodsBrandUnionObject)
			{
				$response['goods_brand_id'] = $gooodsBrandUnionObject->goods_brand_id;

				$gooodsBrand = GoodsBrand::where('id',$gooodsBrandUnionObject->goods_brand_id)->first();

				if($gooodsBrand)
				{
					$response['goods_brand_name'] = $gooodsBrand->brand_name;
				}
			}

			//处理主图
			$goodsMainPictureQuery = GoodsMainPictureUnion::where('goods_id',$this->resource->id);

			$mainPictureCount = $goodsMainPictureQuery->count();

			$response['goods_main_picture_id'] = [];
			$response['goods_main_picture'] = [];

			if($mainPictureCount)
			{
				$goodsMainPicturCollection = $goodsMainPictureQuery->get();

				

				foreach ($goodsMainPicturCollection as $key => $maiPictureItem) 
				{
					$AlbumPictureObject = AlbumPicture::find($maiPictureItem->album_pciture_id);


					// p($AlbumPictureObject);die;
					$picture = null;

					if($AlbumPictureObject->picture_type == 10)
					{
						$picture = asset('storage'.$AlbumPictureObject->picture_path.DIRECTORY_SEPARATOR.$AlbumPictureObject->picture_file);
					}

					if($AlbumPictureObject->picture_type == 20)
					{
						$picture = $AlbumPictureObject->picture_url;
					}

					$response['goods_main_picture_id'][] = $maiPictureItem->id;
					$response['goods_main_picture'][] = ['id'=>$maiPictureItem->id,'picture'=>$picture];
				}
			}

			//处理系统规格和自定义规格
			$systemGoodsSpecNameUnionQuery = GoodsSpecNameUnion::where('goods_id',$this->resource->id)->where('type',10);

			$customGoodsSpecNameUnionQuery = GoodsSpecNameUnion::where('goods_id',$this->resource->id)->where('type',20);

			// 系统规格
			$response['system_spec_name_array'] = [];
			$systemSpecNameArray = [];
			if($systemGoodsSpecNameUnionQuery->count())
			{
				$systemGoodsSpecNameUnionCollection = $systemGoodsSpecNameUnionQuery->get();

				foreach ($systemGoodsSpecNameUnionCollection as $key => $systemGoodsSpecNameUnionItem) 
				{
					$singleItem = [];

					$singleItem['id'] = $systemGoodsSpecNameUnionItem->id;
					$singleItem['goods_class_id'] = $systemGoodsSpecNameUnionItem->goods_class_id;
					$singleItem['goods_spec_name_id'] = $systemGoodsSpecNameUnionItem->goods_spec_name_id;

					$goodsSpecNameObject = GoodsSpecName::find($systemGoodsSpecNameUnionItem->goods_spec_name_id);

					if($goodsSpecNameObject)
					{
						$singleItem['spec_name'] = $goodsSpecNameObject->spec_name;
					}

					$goodsSpecNameValueUnionqQuery = GoodsSpecNameValueUnion::where('goods_id',$this->resource->id)->where('goods_spec_name_id',$systemGoodsSpecNameUnionItem->goods_spec_name_id);

					$goodsSpecNameValueUnionCount = $goodsSpecNameValueUnionqQuery->count();

					$singleItem['specValueArray'] = [];
					$singleItem['selectSpecValueArray'] = [];

					$specValueArray = [];
					$selectSpecValueArray = [];

					if($goodsSpecNameValueUnionCount)
					{
						$goodsSpecNameValueUnionCollection = $goodsSpecNameValueUnionqQuery->get();


						foreach ($goodsSpecNameValueUnionCollection as $key => $goodsSpecNameValueUnionItem) 
						{
							$singleSpecNameValue = [];
							$specValueObject = GoodsSpecValue::find($goodsSpecNameValueUnionItem->goods_spec_value_id);

							if($specValueObject)
							{
								$singleSpecNameValue['id'] = $specValueObject->id;
								$singleSpecNameValue['spec_value'] = $specValueObject->spec_value;
								$singleSpecNameValue['goods_spec_name_id'] = $systemGoodsSpecNameUnionItem->goods_spec_name_id;
								$singleSpecNameValue['isSelect'] = $goodsSpecNameValueUnionItem->is_select;
							}

							$specValueArray[] = $singleSpecNameValue;

							if($singleSpecNameValue['isSelect'])
							{
								$selectSpecValueArray[] =  $singleSpecNameValue;
							}	
						}
					}

					$singleItem['specValueArray'] = $specValueArray;
					$singleItem['selectSpecValueArray'] = $selectSpecValueArray;

					$systemSpecNameArray[] = $singleItem;
				}
			}
			$response['system_spec_name_array'] = $systemSpecNameArray;

			//自定义规格
            $response['custom_spec_name_array'] = [];
			$customSpecNameArray = [];
			
			if($customGoodsSpecNameUnionQuery->count())
			{
				$customGoodsSpecNameUnionCollection = $customGoodsSpecNameUnionQuery->get();

				foreach ($customGoodsSpecNameUnionCollection as $key => $customGoodsSpecNameUnionItem) 
				{
					$singleItem = [];

					$singleItem['id'] = $customGoodsSpecNameUnionItem->id;
					$singleItem['goods_class_id'] = $customGoodsSpecNameUnionItem->goods_class_id;
					$singleItem['goods_spec_name_id'] = $customGoodsSpecNameUnionItem->goods_spec_name_id;

					$goodsSpecNameObject = GoodsSpecName::find($customGoodsSpecNameUnionItem->goods_spec_name_id);

					if($goodsSpecNameObject)
					{
						$singleItem['spec_name'] = $goodsSpecNameObject->spec_name;
					}

					$goodsSpecNameValueUnionqQuery = GoodsSpecNameValueUnion::where('goods_id',$this->resource->id)->where('goods_spec_name_id',$customGoodsSpecNameUnionItem->goods_spec_name_id);

					$goodsSpecNameValueUnionCount = $goodsSpecNameValueUnionqQuery->count();

					$singleItem['specValueArray'] = [];
					$singleItem['selectSpecValueArray'] = [];

					$specValueArray = [];
					$selectSpecValueArray = [];

					if($goodsSpecNameValueUnionCount)
					{
						$goodsSpecNameValueUnionCollection = $goodsSpecNameValueUnionqQuery->get();


						foreach ($goodsSpecNameValueUnionCollection as $key => $goodsSpecNameValueUnionItem) 
						{
							$singleSpecNameValue = [];
							$specValueObject = GoodsSpecValue::find($goodsSpecNameValueUnionItem->goods_spec_value_id);

							if($specValueObject)
							{
								$singleSpecNameValue['id'] = $specValueObject->id;
								$singleSpecNameValue['spec_value'] = $specValueObject->spec_value;
								$singleSpecNameValue['goods_spec_name_id'] = $customGoodsSpecNameUnionItem->goods_spec_name_id;
								$singleSpecNameValue['isSelect'] = $goodsSpecNameValueUnionItem->is_select;
							}

							$specValueArray[] = $singleSpecNameValue;

							if($singleSpecNameValue['isSelect'])
							{
								$selectSpecValueArray[] =  $singleSpecNameValue;
							}	
						}
					}

					$singleItem['specValueArray'] = $specValueArray;
					$singleItem['selectSpecValueArray'] = $selectSpecValueArray;

					$customSpecNameArray[] = $singleItem;
				}
			}
			$response['custom_spec_name_array'] = $customSpecNameArray;

			//处理系统属性和自定义属性
			$systemGoodsAttributeNameUnionQuery = GoodsAttributeNameUnion::where('goods_id',$this->resource->id)->where('type',10);

			$customGoodsAttributeNameUnionQuery = GoodsAttributeNameUnion::where('goods_id',$this->resource->id)->where('type',20);

			// 系统属性
			$response['system_attribute_name_array'] = [];
			
			$systemAttributeNameArray = [];

			if($systemGoodsAttributeNameUnionQuery->count())
			{
				$systemGoodsAttributeNameUnionCollection = $systemGoodsAttributeNameUnionQuery->get();

				foreach ($systemGoodsAttributeNameUnionCollection as $key => $systemGoodsAttributeNameUnionItem) 
				{
					$singleItem = [];

					$singleItem['id'] = $systemGoodsAttributeNameUnionItem->id;
					$singleItem['goods_class_id'] = $systemGoodsAttributeNameUnionItem->goods_class_id;
					$singleItem['goods_attribute_name_id'] = $systemGoodsAttributeNameUnionItem->goods_attribute_name_id;

					$goodsAttributeNameObject = GoodsAttributeName::find($systemGoodsAttributeNameUnionItem->goods_spec_name_id);

					if($goodsAttributeNameObject)
					{
						$singleItem['attribute_name'] = $goodsAttributeNameObject->attribute_name;
					}

					$goodsAttributeNameValueUnionqQuery = GoodsAttributeNameValueUnion::where('goods_id',$this->resource->id)->where('goods_attribute_name_id',$systemGoodsAttributeNameUnionItem->goods_attribute_name_id);

					$goodsAttributeNameValueUnionCount = $goodsAttributeNameValueUnionqQuery->count();

					$singleItem['attributeValueArray'] = [];
					$attributeValueArray = [];
				
					if($goodsAttributeNameValueUnionCount)
					{
						$goodsAttributeNameValueUnionCollection = $goodsAttributeNameValueUnionqQuery->get();


						foreach ($goodsAttributeNameValueUnionCollection as $key => $goodsAttributeNameValueUnionItem) 
						{
							$singleAttributeNameValue = [];
							$attributeValueObject = GoodsAttributeValue::find($goodsAttributeNameValueUnionItem->goods_attribute_value_id);

							if($attributeValueObject)
							{
								$singleAttributeNameValue['id'] = $attributeValueObject->id;
								$singleAttributeNameValue['attribute_value'] = $attributeValueObject->attribute_value;
								$singleAttributeNameValue['goods_attribute_name_id'] = $systemGoodsAttributeNameUnionItem->goods_attribute_name_id;
								$singleAttributeNameValue['isSelect'] = $goodsAttributeNameValueUnionItem->is_select;
							}

							$attributeValueArray[] = $singleAttributeNameValue;
						}
					}

					$singleItem['attributeValueArray'] = $attributeValueArray;
					
					$systemAttributeNameArray[] = $singleItem;
				}
			}
			$response['system_attribute_name_array'] = $systemAttributeNameArray;

			// 自定义属性
			$response['custom_attribute_name_array'] = [];
			
			$customAttributeNameArray = [];

			if($customGoodsAttributeNameUnionQuery->count())
			{
				$customGoodsAttributeNameUnionCollection = $customGoodsAttributeNameUnionQuery->get();

				foreach ($customGoodsAttributeNameUnionCollection as $key => $customGoodsAttributeNameUnionItem) 
				{
					$singleItem = [];

					$singleItem['id'] = $customGoodsAttributeNameUnionItem->id;
					$singleItem['goods_class_id'] = $customGoodsAttributeNameUnionItem->goods_class_id;
					$singleItem['goods_attribute_name_id'] = $customGoodsAttributeNameUnionItem->goods_attribute_name_id;

					$goodsAttributeNameObject = GoodsAttributeName::find($customGoodsAttributeNameUnionItem->goods_spec_name_id);

					if($goodsAttributeNameObject)
					{
						$singleItem['attribute_name'] = $goodsAttributeNameObject->attribute_name;
					}

					$goodsAttributeNameValueUnionqQuery = GoodsAttributeNameValueUnion::where('goods_id',$this->resource->id)->where('goods_attribute_name_id',$customGoodsAttributeNameUnionItem->goods_attribute_name_id);

					$goodsAttributeNameValueUnionCount = $goodsAttributeNameValueUnionqQuery->count();

					$singleItem['attributeValueArray'] = [];
					$attributeValueArray = [];
				
					if($goodsAttributeNameValueUnionCount)
					{
						$goodsAttributeNameValueUnionCollection = $goodsAttributeNameValueUnionqQuery->get();


						foreach ($goodsAttributeNameValueUnionCollection as $key => $goodsAttributeNameValueUnionItem) 
						{
							$singleAttributeNameValue = [];
							$attributeValueObject = GoodsAttributeValue::find($goodsAttributeNameValueUnionItem->goods_attribute_value_id);

							if($attributeValueObject)
							{
								$singleAttributeNameValue['id'] = $attributeValueObject->id;
								$singleAttributeNameValue['attribute_value'] = $attributeValueObject->attribute_value;
								$singleAttributeNameValue['goods_attribute_name_id'] = $customGoodsAttributeNameUnionItem->goods_attribute_name_id;
								$singleAttributeNameValue['isSelect'] = $goodsAttributeNameValueUnionItem->is_select;
							}

							$attributeValueArray[] = $singleAttributeNameValue;
						}
					}

					$singleItem['attributeValueArray'] = $attributeValueArray;
					
					$customAttributeNameArray[] = $singleItem;
				}
			}
			$response['custom_attribute_name_array'] = $customAttributeNameArray;

			//处理产品sku
			$response['goods_sku_array'] = [];

			$goodsSkuQuery = GoodsSku::where('goods_id',$this->resource->id);

			$goodsSkuCount = $goodsSkuQuery->count();

			if($goodsSkuCount)
			{
				$goodsSkuCollection = $goodsSkuQuery->get();

				foreach ($goodsSkuCollection as $key => $goodsSkuItem) 
				{
					$singleSku = [];
					$singleSku['goods_sku_id'] = $goodsSkuItem->id;
					$singleSku['goods_sku'] = $goodsSkuItem->goods_sku;
					$singleSku['goods_sku_code'] = $goodsSkuItem->goods_sku_code;
					$singleSku['goods_sku_line_code'] = $goodsSkuItem->goods_sku_line_code;
					$singleSku['market_price'] = number_format($goodsSkuItem->market_price,2,'.','');
					$singleSku['sale_price'] = number_format($goodsSkuItem->sale_price,2,'.','');
					$singleSku['settle_price'] = number_format($goodsSkuItem->settle_price,2,'.','');
					$singleSku['cost_price'] = number_format($goodsSkuItem->cost_price,2,'.','');
					$singleSku['coin'] = number_format($goodsSkuItem->coin,2,'.','');
					$singleSku['deduct_coin'] = number_format($goodsSkuItem->deduct_coin,2,'.','');
					$singleSku['share_benefit_type'] = $goodsSkuItem->share_benefit_type;
					$singleSku['share_benefit_money'] = number_format($goodsSkuItem->share_benefit_money,2,'.','');
					$singleSku['share_benefit_rate'] = bcmul($goodsSkuItem->share_benefit_rate,100);
					$singleSku['store_number'] = $goodsSkuItem->sku_store_number;
					$singleSku['store_warning_number'] = $goodsSkuItem->store_warning_number;

					//处理规格参数
					$singleSku['spec_array'] = [];

					$goodsSkuSpecValueQuery = GoodsSkuSpecValueUnion::where('goods_sku_id',$goodsSkuItem->id);

					if($goodsSkuSpecValueQuery->count())
					{
						$goodsSkuSpecValueCollection = $goodsSkuSpecValueQuery->get();

						foreach ($goodsSkuSpecValueCollection as $key => $goodsSkuSpecValueItem) 
						{
							$singleSku["spec_value_{$key}_object"] = [];
							$gingleSkuSpec = [];

							$goodsSpecValueObject = GoodsSpecValue::find($goodsSkuSpecValueItem->goods_spec_value_id);

							if($goodsSpecValueObject)
							{
								$gingleSkuSpec['id'] = $goodsSpecValueObject->id;
								$gingleSkuSpec['spec_value'] = $goodsSpecValueObject->spec_value;
								$gingleSkuSpec['goods_spec_name_id'] = $goodsSkuSpecValueItem->goods_spec_name_id;
							}

							$singleSku['spec_array'][] = $gingleSkuSpec;
							$singleSku["spec_value_{$key}_object"] = $gingleSkuSpec;
							
						}
					}

					//处理sku图片
					$singleSku['sku_picture_id_array'] = [];
					$singleSku['sku_picture_array'] = [];

					$goodsSkuPictureUnionCollection =  GoodsSkuPictureUnion::where('goods_sku_id',$goodsSkuItem->id)->get();

					//p($goodsSkuPictureUnionCollection);die;


					foreach ($goodsSkuPictureUnionCollection as $key => $goodsSkuPictureUnionItem) 
					{
						$singleSku['sku_picture_id_array'][] = $goodsSkuPictureUnionItem->album_pciture_id;
						
						$skuAlbumPictureObject = AlbumPicture::find($goodsSkuPictureUnionItem->album_pciture_id);

						$picture = null;

						if($skuAlbumPictureObject->picture_type == 10)
						{
							$picture = asset('storage'.$skuAlbumPictureObject->picture_path.DIRECTORY_SEPARATOR.$skuAlbumPictureObject->picture_file);
						}

						if($skuAlbumPictureObject->picture_type == 20)
						{
							$picture = $skuAlbumPictureObject->picture_url;
						}

						$singleSku['sku_picture_array'][] = ['id'=>$goodsSkuPictureUnionItem->album_pciture_id,'picture'=>$picture ];
					}

					$response['goods_sku_array'][] = $singleSku;

				}
			}

			//处理发货地址

			$response['send_region_array'] = [];
			$goodsSendAddressObject = GoodsSendAddress::where('goods_id',$this->resource->id)->first();

			if($goodsSendAddressObject)
			{
				$response['send_region_array'][] = $goodsSendAddressObject->province_id;
				$response['send_region_array'][] = $goodsSendAddressObject->city_id;
				$response['send_region_array'][] = $goodsSendAddressObject->region_id;
				$response['send_address_info'] = $goodsSendAddressObject->address_info;
			}

			//处理产品详情
			$goodsInfoObject = GoodsInfo::where('goods_id',$this->resource->id)->first();

			if($goodsInfoObject)
			{
				$response['goods_description'] = htmlspecialchars_decode($goodsInfoObject->goods_description);
			}
        }

        return $response;
    }


}
