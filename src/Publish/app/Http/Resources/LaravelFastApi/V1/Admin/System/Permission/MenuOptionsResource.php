<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-09-09 11:58:13
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-09-09 12:29:38
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\MenuOptionsResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuOptionsResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    // public static $replaceType;

    // public static function setReplaceType($replaceType = 10)
    // {
    //     self::$replaceType = $replaceType;
    // }

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
			'value'=>$this->resource->id,
			'label'=>$this->resource->meta_title
		];

		if(isset($this->resource->children) && count($this->resource->children))
		{
			//p($this->resource['children']);
			$response['children'] = $this->collection($this->resource->children);
		}

        return $response;
    }


}
