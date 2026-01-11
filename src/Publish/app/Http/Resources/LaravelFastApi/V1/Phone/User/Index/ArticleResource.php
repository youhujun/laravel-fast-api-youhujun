<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-04-29 20:17:48
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-04-29 20:26:33
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Phone\User\Index\ArticleResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Phone\User\Index;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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

       $response = [];

        if(\is_object($this->resource))
        {
            $response = [
                'article_id'=>$this->resource->id,
                'title'=>$this->resource->title,
                'status'=>$this->resource->status,
                'type'=>$this->resource->type,
                'is_top'=>$this->resource->is_top,
                'check_status'=>$this->resource->check_status,
                'type'=>$this->resource->type,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort
			];
            
        }

        return $response;
    }


}
