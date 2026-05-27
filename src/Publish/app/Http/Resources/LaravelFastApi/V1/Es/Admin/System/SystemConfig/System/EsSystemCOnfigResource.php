<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-17 10:44:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-17 10:45:15
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\System\EsSystemCOnfigResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\System;

use Illuminate\Http\Resources\Json\JsonResource;

class EsSystemCOnfigResource extends JsonResource
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

        $valueMApArray = [10 => $this->resource->item_value,20 => $this->resource->item_value,30 => $this->resource->item_price,40 => $this->resource->item_path];

        $response = [
            'id' => $this->resource->id,
            'item_type' => $this->resource->item_type,
            'item_label' => $this->resource->item_label,
            'item_value' => $valueMApArray[$this->resource->item_type],
            'item_introduction' => $this->resource->item_introduction,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'sort' => $this->resource->sort,
        ];


        return $response;
    }
}
