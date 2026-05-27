<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-12 06:41:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-12 13:30:27
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserAddress\EsUserAddressResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserAddress;

use Illuminate\Http\Resources\Json\JsonResource;

class EsUserAddressResource extends JsonResource
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
            'user_address_uid' =>  $this->resource->_docId ?? 0,
            'created_at' =>  $this->resource->created_at ?? null,
            'is_default' =>  $this->resource->is_default ?? 0,
            'is_top' =>  $this->resource->is_top ?? 0,
            'user_uid'=>$this->resource->user_uid??0,
            'user_name' =>  $this->resource->user_name ?? '',
            'phone' =>  $this->resource->phone ?? '',
            'country_id' =>  $this->resource->country_id ?? 0,
            'province_id' =>  $this->resource->province_id ?? 0,
            'region_id' =>  $this->resource->region_id ?? 0,
            'city_id' =>  $this->resource->city_id ?? 0,
            'address_info' =>  $this->resource->address_info ?? '',
            'address_type' =>  $this->resource->address_type ?? 0,
            'province_name' => $this->resource->province_name ?? '',
            'region_name' => $this->resource->region_name ?? '',
            'city_name' => $this->resource->city_name ?? '',
        ];

        return $response;
    }
}
