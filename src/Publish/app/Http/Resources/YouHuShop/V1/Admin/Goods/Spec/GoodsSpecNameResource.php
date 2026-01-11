<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-13 20:16:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-08 20:35:02
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Spec\GoodsSpecNameResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Spec;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsSpecNameResource extends JsonResource
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

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'is_default_option'=>$this->resource->is_default_option,
                'spec_type'=>$this->resource->spec_type,
                'spec_name'=>$this->resource->spec_name,
                'spec_code'=>$this->resource->spec_code,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort
            ];

        }

        return $response;
    }


}
