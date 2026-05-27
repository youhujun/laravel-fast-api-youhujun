<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-20 04:50:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 14:34:05
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Region\EsRegionResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Region;

use Illuminate\Http\Resources\Json\JsonResource;

class EsRegionResource extends JsonResource
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
     * 控制是否显示详情
     *
     * @var [type]
     */
    public static $showInfo ;

    public static function showControl($showInfo = 0)
    {
        self::$showInfo = $showInfo;
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
            'id' => $this->resource->id,
            'region_name' => $this->resource->region_name,
        ];

        if (self::$showInfo) {
            $response['parent_id'] = $this->resource->parent_id;
            $response['deep'] = $this->resource->deep;
            $response['region_area'] = $this->resource->region_area;
            $response['created_at'] = $this->resource->created_at;
            $response['sort'] = $this->resource->sort;
        }

        if (isset($this->resource->children) && count($this->resource->children)) {
            //p($this->resource['children']);
            $response['children'] = $this->collection($this->resource->children);
        }


        return $response;
    }
}
