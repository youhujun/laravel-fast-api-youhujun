<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 01:06:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-16 01:13:40
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission\EsMenuResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class EsMenuResource extends JsonResource
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
        //p($this->resource);die;
        $response = [
            'id' => $this->resource->id,
            'parent_id' => $this->resource->parent_id,
            'deep' => $this->resource->deep,
            'type' => $this->resource->type,
            'route_name' => $this->resource->route_name,
            'route_path' => $this->resource->route_path,
            'component' => $this->resource->component,
            'hidden' => $this->resource->hidden,
            'always_show' => $this->resource->always_show,
            'perm' => $this->resource->permission_tag,
            'switch' => $this->resource->switch,
            'sort' => $this->resource->sort,
            'icon' => $this->resource->meta_icon,
            'title' => $this->resource->meta_title,
            'cache' => $this->resource->meta_no_cache,
            'affix' => $this->resource->meta_affix,
            'breadcrumb' => $this->resource->meta_breadcrumb,
            'active_menu' => $this->resource->meta_active_menu,
        ];

        //如果有跳转路径才加载,没有就不加
        if (trim($this->resource->redirect) && !empty($this->resource->redirect)) {
            $response['redirect'] = $this->resource->redirect;
        }

        if (isset($this->resource->children) && count($this->resource->children)) {
            //p($this->resource['children']);
            $response['children'] = $this->collection($this->resource->children);
        }

        return $response;
    }
}
