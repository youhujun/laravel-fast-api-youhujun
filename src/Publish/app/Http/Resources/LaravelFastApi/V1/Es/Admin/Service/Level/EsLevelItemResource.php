<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-18 15:01:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-18 15:29:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\EsLevelItemResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level;

use Illuminate\Http\Resources\Json\JsonResource;

class EsLevelItemResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

    //禁止中文转 Unicode
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;

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
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'item_name' => $this->resource->item_name,
            'item_code' => $this->resource->item_code,
            'description' => $this->resource->description,
            'created_at' => $this->resource->created_at,
            'sort' => $this->resource->sort ?? 100,
        ];


        return $response;
    }
}
