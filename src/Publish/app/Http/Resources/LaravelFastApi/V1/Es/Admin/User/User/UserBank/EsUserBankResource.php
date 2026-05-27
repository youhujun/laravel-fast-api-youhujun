<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-13 10:38:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-13 10:38:43
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\User\UserBank\EsUserBankResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\User\UserBank;

use Illuminate\Http\Resources\Json\JsonResource;

class EsUserBankResource extends JsonResource
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
            'user_uid' => $this->resource->user_uid,
            'user_bank_uid' => $this->resource->user_bank_uid,
            'bank_id' => $this->resource->bank_id,
            'bank_name' => $this->resource->bank_name,
            'bank_front_uid' => $this->resource->bank_front_uid ?? 0,
            'bank_back_uid' => $this->resource->bank_back_uid ?? 0,
            'bank_front_picture' => $this->resource->bank_front_picture ?? null,
            'bank_back_picture' => $this->resource->bank_back_picture ?? null,
            'is_default' => $this->resource->is_default,
            'bank_number' => $this->resource->bank_number ?? null,
            'bank_account' => $this->resource->bank_account ?? null,
            'bank_address' => $this->resource->bank_address ?? null,
            'sort' => $this->resource->sort,
            'created_at' => $this->resource->created_at,
        ];


        return $response;
    }
}
