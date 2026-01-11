<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-14 19:03:55
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-14 19:10:08
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\GoodsClass\GoodsClassSpecNameSystemUnionResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\GoodsClass;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\YouHuShop\V1\Goods\Spec\GoodsSpecName;

class GoodsClassSpecNameSystemUnionResource extends JsonResource
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
                'goods_class_id'=>$this->resource['goods_class_id'],
                'goods_spec_name_id'=>$this->resource['goods_spec_name_id'],
                'created_at'=>$this->resource['created_at'],
            ];

          
        }

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'goods_class_id'=>$this->resource->goods_class_id,
                'goods_spec_name_id'=>$this->goods_spec_name_id,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];

			$goodsSpecName = GoodsSpecName::find($this->goods_spec_name_id);

			if($goodsSpecName)
			{
				$response['spec_name'] = $goodsSpecName->spec_name;
				$response['spec_code'] = $goodsSpecName->spec_code;
			}
        }

        return $response;
    }


}
