<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-19 09:40:38
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-25 09:25:46
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Goods\GoodsClassResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\Goods;

use Illuminate\Http\Resources\Json\JsonResource;

class GoodsClassResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;


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

	    $response = [
			'id'=>$this->resource->id,
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'switch'=>$this->resource->switch,
			'rate'=>bcmul($this->resource->rate,100,0),
			'goods_class_name'=>$this->resource->goods_class_name,
			'goods_class_code'=>$this->resource->goods_class_code,
			'is_certificate'=>$this->resource->is_certificate,
			'certificate_number'=>$this->resource->certificate_number,
			'note'=>$this->resource->note,
			'created_at'=>$this->resource->created_at,
			'sort'=>$this->resource->sort
		];

		if(isset($this->resource->children) && count($this->resource->children))
		{
			//p($this->resource['children']);
			$response['children'] = $this->collection($this->resource->children);
		}

		if(isset($this->resource->picture))
        {
			$response['picture'] = asset('storage'.$this->resource->picture->picture_path.'/'.$this->resource->picture->picture_file);
		}

       

        return $response;
    }


}
