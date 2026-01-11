<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-03 17:30:54
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 17:35:12
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\User\UserInfo\UserAmountResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\User\UserInfo;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAmountResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

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
			'id'=>$this->resource->id,
			'user_id'=>$this->resource->user_id,
			'userId'=>$this->resource->userId,
			'amount'=>$this->resource->amount,
			'bonus'=>$this->resource->bonus,
			'prepare_bonus'=>$this->resource->prepare_bonus,
			'coin'=>$this->resource->coin,
			'amount'=>$this->resource->amount,
			'score'=>$this->resource->score,
			'created_at'=>$this->resource->created_at,
			'sort'=>$this->resource->sort,
		];

        return $response;
    }


}
