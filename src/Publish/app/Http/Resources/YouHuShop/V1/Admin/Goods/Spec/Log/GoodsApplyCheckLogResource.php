<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-28 11:26:19
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-28 11:34:43
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Spec\Log\GoodsApplyCheckLogResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Spec\Log;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsApplyCheckLogResource extends JsonResource
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
                'goods_id'=>$this->resource['goods_id'],
                'type'=>$this->resource['type'],
                'config_name'=>$this->resource['config_name'],
            ];

        }

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'goods_id'=>$this->resource->goods_id,
                'status'=>$this->resource->status,
                'apply_type'=>$this->resource->apply_type,
                'check_apply_at'=>$this->resource->check_apply_at,
                'check_at'=>$this->resource->check_at,
                'refuse_info'=>$this->resource->refuse_info,
            ];
        }

        return $response;
    }


}
