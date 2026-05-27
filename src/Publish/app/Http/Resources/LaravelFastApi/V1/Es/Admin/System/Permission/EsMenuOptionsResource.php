<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-04-16 10:30:59
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 10:31:47
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsMenuOptionsResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class EsMenuOptionsResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

	//禁止中文转 Unicode
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;


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
