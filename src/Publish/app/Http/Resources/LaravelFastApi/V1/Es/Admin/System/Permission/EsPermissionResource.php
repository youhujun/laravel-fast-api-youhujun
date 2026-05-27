<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 01:32:58
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 05:18:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsPermissionResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsPermissionResource extends JsonResource
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
        $response = [
            'id' => $this->resource->id,
            'parent_id' => $this->resource->parent_id,
            'deep' => $this->resource->deep,
            'name' => $this->resource->route_name,
            'path' => $this->resource->route_path,
            'component' => $this->resource->component
        ];

        $response['meta'] = [
            'title' => $this->resource->meta_title,
            'icon' => $this->resource->meta_icon,
            'hidden' => (bool)$this->resource->hidden,
            'keepAlive' => (bool)$this->resource->meta_no_cache,
            'alwaysShow' => (bool)$this->resource->always_show,
            'roles' => $this->resource->rolesLogicName ?? [],
        ];

        if (!empty(trim($this->resource->redirect))) {
            $response['redirect'] = $this->resource->redirect;
        }

        if (isset($this->resource->children) && $this->resource->children->isNotEmpty()) {
            $response['children'] = $this->collection($this->resource->children);
        }

        return $response;
    }
}
