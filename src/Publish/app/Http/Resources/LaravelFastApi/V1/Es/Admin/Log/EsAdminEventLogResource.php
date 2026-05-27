<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-25 22:49:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-25 23:56:37
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Log\EsAdminEventLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Log;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsAdminEventLogResource extends JsonResource
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
            'admin_event_log_uid' => $this->resource->admin_event_log_uid,
            'admin_uid' => $this->resource->admin_uid,
            'data_type' => $this->resource?->data_type ?? 0,
            'event_type' => $this->resource->event_type,
            'event_code' => $this->resource->event_code,
            'event_route_action' => $this->resource->event_route_action,
            'event_name' => $this->resource->event_name,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];

        $adminIndexName = config('common_es.indices.user.admins');

        $esAdminObject = EsQueryFacade::index($adminIndexName)->where('admin_uid', $this->resource->admin_uid)->get()->first();

        // p($esAdminObject);
        // die;

        if ($esAdminObject) {
            $response['admin_account_name'] = $esAdminObject->account_name;
            $response['admin_nick_name'] = $esAdminObject->nick_name;
            $response['admin_real_name'] = $esAdminObject->real_name;
            $response['admin_phone'] = $esAdminObject->phone;
        }

        return $response;
    }
}
