<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-18 12:40:44
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-18 12:40:55
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform\EsSystemDouyinConfigResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Http\Resources\Json\JsonResource;

class EsSystemDouyinConfigResource extends JsonResource
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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'type' => $this->resource->type,
            'appid' => $this->resource->appid,
            'appsecret' => $this->resource->appsecret,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at,
            'sort' => $this->resource->sort
        ];

        return $response;
    }
}
