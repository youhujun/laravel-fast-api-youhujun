<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 08:38:19
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:17:56
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Service\Level\UserLevelItemUnionResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLevelItemUnionResource extends JsonResource
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
            'id'=>$this->id,
            'sort'=>$this->sort,
            'user_level_id'=>$this->user_level_id,
            'level_item_id'=>$this->level_item_id,
            'value'=>$this->value,
            'value_type'=>$this->value_type
        ];


        return $response;
    }


}
