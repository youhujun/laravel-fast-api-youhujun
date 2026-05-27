<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-18 14:03:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-18 14:04:04
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Bank\EsBankResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Bank;

use Illuminate\Http\Resources\Json\JsonResource;

class EsBankResource extends JsonResource
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
            'id' => $this->id,
            'created_at' => $this->created_at,
            'bank_name' => $this->bank_name,
            'bank_code' => $this->bank_code,
            'is_default' => $this->is_default,
            'sort' => $this->sort
        ];

        return $response;
    }
}
