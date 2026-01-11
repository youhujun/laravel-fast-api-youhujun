<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-02 10:58:48
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\User\UserIdCardResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\Picture\AlbumPictureResource;

class UserIdCardResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $response = [];

		$response = [
			'id' => $this->resource->id,
			'user_id' => $this->resource->user_id,
			'id_card_front_id' => $this->resource->id_card_front_id,
			'id_card_back_id' => $this->resource->id_card_back_id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'sort' => $this->resource->sort,
		];

		if($this->resource->relationLoaded('cardFront'))
		{
			if(!is_null($this->resource->cardFront))
			{
				$response['card_front'] = new AlbumPictureResource($this->resource->cardFront);
			}
		}

		if($this->resource->relationLoaded('cardBack'))
		{
			if(!is_null($this->resource->cardBack))
			{
				$response['card_back'] = new AlbumPictureResource($this->resource->cardBack);
			}
		}

        return $response;
    }


}
