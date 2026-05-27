<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-11 14:05:31
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-11 14:36:49
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo\EsUserAmountArrayResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class EsUserAmountArrayResource extends JsonResource
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
            'user_amount_uid' => $this->resource->user_amount_uid ?? 0,
            'user_uid' => $this->resource->user_uid ?? 0,
            'amount' => bcdiv($this->resource->amount, 100, 2)  ?? 0,
            'bonus' => bcdiv($this->resource->bonus, 100, 2) ?? 0,
            'prepare_bonus' => bcdiv($this->resource->prepare_bonus, 100, 2) ?? 0,
            'coin' =>   bcdiv($this->resource->coin, 100, 2) ?? 0,
            'score' =>   bcdiv($this->resource->score, 100, 2) ?? 0,
            'created_at' =>  $this->resource->created_at ?? null,
            'sort' =>  $this->resource->sort ?? 0
        ];




        return $response;
    }
}
