<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-26 19:26:55
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-26 19:42:33
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Collect\GoodsInfoCollectResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Collect;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\User\Collect\UserCollectGoods;
use App\Models\User\Collect\UserCollectShop;

class GoodsInfoCollectResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

	public $user_id = 0;

	public $goods_id = 0;

	public $shop_id = 0;

    public static $replaceType;

    public static function setReplaceType($replaceType = 10)
    {
        self::$replaceType = $replaceType;
    }

	public function __construct($user_id,$goods_id,$shop_id)
	{
		$this->user_id = $user_id;
		$this->goods_id = $goods_id;
		$this->shop_id = $shop_id;
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

	    $userCollectGoodsCount =  UserCollectGoods::where('goods_id',$this->goods_id)->where('user_id',$this->user_id)->count();

	    $userCollectShopCount =  UserCollectShop::where('shop_id',$this->shop_id)->where('user_id',$this->user_id)->count();

		 $response = [
				//是否收藏商品
                'is_collect_goods'=> 0,
				// 是否收藏店铺
                'is_collect_shop'=> 0,
        ];

		if($userCollectGoodsCount)
		{
			$response['is_collect_goods'] = 1;
		}

		if($userCollectShopCount)
		{
			$response['is_collect_shop'] = 1;
		}

        return $response;
    }


}
