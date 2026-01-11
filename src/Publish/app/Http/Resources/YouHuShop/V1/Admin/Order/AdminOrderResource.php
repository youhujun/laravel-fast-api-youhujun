<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-10-18 11:49:48
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-19 11:18:58
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Order\AdminOrderResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Order;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\User\Info\UserInfo;

use App\Models\Order\OrderAddress;
use App\Models\System\Region\Region;

use App\Models\Order\Union\OrderGoodsUnion;
use App\Models\Order\Union\OrderGoodsSkuUnion;

use App\Models\Goods\Goods;
use App\Models\Goods\GoodsSku;

use App\Models\Goods\Union\GoodsMainPictureUnion;
use App\Models\Goods\Union\GoodsSkuPictureUnion;

use App\Models\Picture\AlbumPicture;

class AdminOrderResource extends JsonResource
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

        // if(\is_array($this->resource))
        // {
        //      $response = [
        //         'id'=>$this->resource['id'],
        //         'is_show'=>$this->resource['is_show'],
        //         'type'=>$this->resource['type'],
        //         'config_name'=>$this->resource['config_name'],
        //     ];

        //     if(isset($this->resource['children']) && count($this->resource['children']))
        //     {
        //         //p($this->resource['children']);
        //         $response['children'] = $this->collection($this->resource['children']);
        //     }
        // }

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'parent_id'=>$this->resource->parent_id,
                'order_deep'=>$this->resource->order_deep,
                'payer_id'=>$this->resource->payer_id,
                'shop_id'=>$this->resource->shop_id,
                'time_type'=>$this->resource->time_type,
                'pay_type'=>$this->resource->pay_type,
                'order_status'=>$this->resource->order_status,
                'is_use_balance'=>$this->resource->is_use_balance,
                'is_use_coin'=>$this->resource->is_use_coin,
                'is_error'=>$this->resource->is_error,
                'error_type'=>$this->resource->error_type,
                'order_sn'=>$this->resource->order_sn,
                'pay_sn'=>$this->resource->pay_sn,
                'order_amount'=> number_format($this->resource->order_amount,2,'.',''),
                'order_coin'=>number_format($this->resource->order_coin,2,',',''),
                'real_pay_amount'=>number_format($this->resource->real_pay_amount,2,',',''),
                'real_pay_coin'=>number_format($this->resource->real_pay_coin,2,',',''),
                'balance_limit'=>number_format($this->resource->balance_limit,2,',',''),
                'deduct_coin_limit'=>number_format($this->resource->deduct_coin_limit,2,',',''),
                'reark_info'=>$this->resource->reark_info,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort
            ];

			$payerInfo = UserInfo::where('user_id',$this->resource->payer_id)->first();

			if($payerInfo)
			{
				if($payerInfo->real_name)
				{
					$response['payer_name'] = $payerInfo->real_name;
				}
				else
				{
					$response['payer_name'] = $payerInfo->nick_name;
				}
			}

			$orderAddress = OrderAddress::where('order_id',$this->resource->id)->first();

			if($orderAddress)
			{
				$address = [];

				$province = Region::find($orderAddress->province_id);

				$province_name = $province->region_name;

				$region = Region::find($orderAddress->region_id);

				$region_name = $region->region_name;

				$city = Region::find($orderAddress->city_id);

				$city_name = $city->city_name;

				$address['province'] = $province_name;
				$address['region'] = $region_name;
				$address['city'] = $city_name;
				$address['address_info'] = $orderAddress->address_info;

				$response['address'] = $address;

			}

			//订单产品数组
			$orderGoodsArray = [];

			$orderGoodsUnionQyery = OrderGoodsUnion::where('order_id',$this->resource->id);

			$orderGoodsUnionCount = $orderGoodsUnionQyery->count();

			if($orderGoodsUnionCount)
			{
				$orderGoodsCollection = $orderGoodsUnionQyery->get();

				foreach ($orderGoodsCollection as $key => $orderGoodsUnionItem) 
				{
					$goods = Goods::find($orderGoodsUnionItem->goods_id);

					$goodsMainPictureUnion = GoodsMainPictureUnion::where('goods_id',$goods->id)->first();

					$albumPcitrue = AlbumPicture::find($goodsMainPictureUnion->album_pciture_id);

					$picture = '';

					if($albumPcitrue->picture_type == 10)
					{
						$picture = asset('storage/'.$albumPcitrue->picture_path.DIRECTORY_SEPARATOR.$albumPcitrue->picture_file);
					}

					if($albumPcitrue->picture_type == 20)
					{
						$picture = $albumPcitrue->picture_url;
					}

					$goodsItem = [];

					$goodsItem['goods_name'] = $goods->goods_name;
					$goodsItem['sale_price'] = number_format($goods->sale_price,2,',','');
					$goodsItem['buy_number'] = $orderGoodsUnionItem->buy_number;
					$goodsItem['picture'] = $picture;

					$orderGoodsArray[] = $goodsItem;
				}
			}

			$response['goods_array'] = $orderGoodsArray;

			//订单产品sku数组
			$orderGoodsSkuArray = [];

			$orderGoodsSkuUnionQuery = OrderGoodsSkuUnion::where('order_id',$this->resource->id);

			$orderGoodsSkuUnionCount = $orderGoodsSkuUnionQuery->count();

			if($orderGoodsSkuUnionCount)
			{
				$orderGoodsSkuUnionCollection = $orderGoodsSkuUnionQuery->get();

				foreach ($orderGoodsSkuUnionCollection as $key => $orderGoodsSkuUnionItem) 
				{
					$goodsSku = GoodsSku::find($orderGoodsSkuUnionItem->goods_sku_id);

					$goods = Goods::find($goodsSku->goods_id);

					$goodsSkuUnion = GoodsSkuUnion::where('goods_sku_id',$goodsSku->id)->first();

					$albumPcitrue = AlbumPicture::find($goodsSkuUnion->album_pciture_id);

					$picture = '';

					if($albumPcitrue->picture_type == 10)
					{
						$picture = asset('storage/'.$albumPcitrue->picture_path.DIRECTORY_SEPARATOR.$albumPcitrue->picture_file);
					}

					if($albumPcitrue->picture_type == 20)
					{
						$picture = $albumPcitrue->picture_url;
					}

					$goodsSkuItem = [];

					$goodsSkuItem['goods_name'] = $goods->goods_name;
					$goodsSkuItem['sale_price'] = number_format($goodsSku->sale_price,2,',','');
					$goodsSkuItem['buy_number'] = $orderGoodsSkuUnionItem->buy_number;
					$goodsSkuItem['buy_number'] = $picture;

					$orderGoodsSkuArray[] = $goodsSkuItem;
				}
			}

			$response['goods_sku_array'] = $orderGoodsSkuArray;

            /* if($this->resource->relationLoaded('unionResource'))
            {
                if(!is_null($this->rescource))
                {
                    $response['union_rescource'] = new ReplaceUnionResource($this->rescource);
                }
            }

            if($this->resource->relationLoaded('unionCollection'))
            {
                if(!is_null($this->collection))
                {
                    $response['union_collection'] = ReplaceUnionResource::collection($this->collection);
                }
            } */
        }

        return $response;
    }


}
