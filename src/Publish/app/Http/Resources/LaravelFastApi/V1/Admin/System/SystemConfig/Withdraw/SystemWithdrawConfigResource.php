<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 15:13:39
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 15:24:38
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Withdraw\SystemWithdrawConfigResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\SystemConfig\Withdraw;

use Illuminate\Http\Resources\Json\JsonResource;

class SystemWithdrawConfigResource extends JsonResource
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
			'item_name'=>$this->resource->item_name,
			'item_value'=>$this->resource->item_value,
			'value_type'=>$this->resource->value_type,
		];

		if($this->resource->value_type == 20){
			$response['item_value'] = bcmul($this->resource->item_value,100);
		}

        return $response;
    }


}
