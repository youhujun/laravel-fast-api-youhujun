<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-01 17:25:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 23:23:47
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\Template\V1\Es\Replace\EsReplaceResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */


namespace App\Http\Resources\Template\V1\Es\Replace;

use Illuminate\Http\Resources\Json\JsonResource;

class EsReplaceResource extends JsonResource
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

		$response = [
			'id'=>$this->resource->id,
		];

		if($this->resource->relationLoaded('unionResource'))
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
		}
	

        return $response;
    }


}
