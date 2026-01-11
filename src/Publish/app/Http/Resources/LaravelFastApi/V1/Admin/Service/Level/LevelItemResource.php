<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 08:38:19
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:16:54
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Service\Level\LevelItemResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Admin\Service\Level\UserLevelItemUnionResource;

class LevelItemResource extends JsonResource
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
            'created_at'=>$this->created_at,
           /*  'updated_at'=>$this->updated_at, */
            'sort'=>$this->sort,
            'type'=>$this->type,
            'item_name'=>$this->item_name,
            'item_code'=>$this->item_code,
            'description'=>$this->description,
        ];


        if($this->resource->relationLoaded('userLevelItemValue'))
        {
            if(!is_null($this->userLevelItemValue))
            {
                $response['user_level_item_value'] = new UserLevelItemUnionResource($this->userLevelItemValue);
            }
        }

        return $response;
    }


}
