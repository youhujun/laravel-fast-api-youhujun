<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 20:10:57
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 17:00:12
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\User\UserAccountLogResource.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountLogResource extends JsonResource
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

		$response = [
			'id' => $this->resource->id ,
			'user_id' => $this->resource->user_id ,
			'before_amount' => $this->resource->before_amount,
			'change_value' => $this->resource->change_value,
			'change_type' => $this->resource->change_type,
			'amount' => $this->resource->amount,
			'note' => $this->resource->note ,
			'created_at' => $this->resource->created_at ,
			'updated_at' => $this->resource->updated_at ,
			'sort' => $this->resource->sort ,
		];

        return $response;
    }


}
