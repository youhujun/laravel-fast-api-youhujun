<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-18 23:25:41
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-19 01:45:24
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Spec\GoodsSpecValueResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Spec;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsSpecValueResource extends JsonResource
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
                'spec_value'=>$this->resource['spec_value'],
                'goods_spec_name_id'=>$this->resource['goods_spec_name_id'],
                'spec_value_color'=>$this->resource['spec_value_color'],
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
                'spec_value'=>$this->resource->spec_value,
                'goods_spec_name_id'=>$this->resource->goods_spec_name_id,
                'spec_value_color'=>$this->resource->spec_value_color,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];
        }

        return $response;
    }


}
