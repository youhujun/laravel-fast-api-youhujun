<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-21 01:45:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-21 02:47:36
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Attribute\GoodsAttributeValueResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Attribute;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsAttributeValueResource extends JsonResource
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
                'admin_id'=>$this->resource['admin_id'],
                'shop_id'=>$this->resource['shop_id'],
                'server_id'=>$this->resource['server_id'],
                'value_type'=>$this->resource['value_type'],
                'attribute_value'=>$this->resource['attribute_value'],
                'goods_attribute_name_id'=>$this->resource['goods_attribute_name_id'],
                'note'=>$this->resource['note'],
                'created_at'=>$this->resource['created_at'],
                'sort'=>$this->resource['sort'],
            ];
        }

        if(\is_object($this->resource))
        {
           	$response = [
                'id'=>$this->resource->id,
                'admin_id'=>$this->resource->admin_id,
                'shop_id'=>$this->resource->shop_id,
                'server_id'=>$this->resource->server_id,
                'value_type'=>$this->resource->value_type,
                'attribute_value'=>$this->resource->attribute_value,
                'goods_attribute_name_id'=>$this->resource->goods_attribute_name_id,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];
        }

        return $response;
    }


}
