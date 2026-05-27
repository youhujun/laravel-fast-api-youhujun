<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-11 15:43:19
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-11 16:07:31
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo\EsUserAccountLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class EsUserAccountLogResource extends JsonResource
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
            'user_uid' => $this->resource->user_uid ?? null ,
            'data_type' => $this->resource->data_type ?? null,
            'before_amount' => bcdiv($this->resource->before_amount, 100, 2) ?? 0,
            'change_value' => bcdiv($this->resource->change_value, 100, 2) ?? 0,
            'change_type' => $this->resource->change_type ?? null,
            'amount' => bcdiv($this->resource->amount, 100, 2) ?? 0,
            'note' => $this->resource->note ?? null ,
            'created_at' => $this->resource->created_at ?? null ,
            'updated_at' => $this->resource->updated_at ?? null ,
            'sort' => $this->resource->sort ?? null ,
        ];

        if (isset($this->resource->user_amount_log_uid)) {
            $response['user_amount_log_uid'] = $this->resource->user_amount_log_uid ?? null;
        }

        if (isset($this->resource->user_score_log_uid)) {
            $response['user_score_log_uid'] = $this->resource->user_score_log_uid ?? null;
        }

        if (isset($this->resource->user_coin_log_uid)) {
            $response['user_coin_log_uid'] = $this->resource->user_coin_log_uid ?? null;
        }

        return $response;
    }
}
