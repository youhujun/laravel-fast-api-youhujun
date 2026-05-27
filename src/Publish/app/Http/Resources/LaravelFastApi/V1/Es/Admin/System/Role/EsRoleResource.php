<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-19 19:35:45
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-06 08:04:46
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Role\EsRoleResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class EsRoleResource extends JsonResource
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
            'id' => $this->resource->id,
            'parent_id' => $this->resource->parent_id,
            'deep' => $this->resource->deep,
            'type' => $this->resource->type,
            'is_system' => $this->resource->is_system,
            'switch' => $this->resource->switch,
            'role_name' => $this->resource->role_name,
            'logic_name' => $this->resource->logic_name,
            'sort' => $this->resource->sort,
            'permission_array' => $this->resource->permission_array ?? [],
        ];

        if (isset($this->resource->children) && count($this->resource->children)) {
            //p($this->resource['children']);
            $response['children'] = $this->collection($this->resource->children);
        }


        return $response;
    }
}
