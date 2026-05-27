<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 04:57:07
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-22 16:40:04
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log\EsUserEventLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsUserEventLogResource extends JsonResource
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
            'user_event_log_uid' => isset($this->resource?->user_event_log_uid) ? $this->resource->user_event_log_uid : null,
            'user_uid' => $this->resource->user_uid,
            'data_type' => $this->resource?->data_type ?? 0,
            'event_type' => $this->resource->event_type,
            'event_code' => $this->resource->event_code,
            'event_route_action' => $this->resource->event_route_action,
            'event_name' => $this->resource->event_name,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];

        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $this->resource->user_uid)->get()->first();

        // p($esAdminObject);
        // die;

        if ($esUserObject) {
            $response['admin_account_name'] = $esUserObject->account_name;
            $response['admin_nick_name'] = $esUserObject->nick_name;
            $response['admin_real_name'] = $esUserObject->real_name;
            $response['admin_phone'] = $esUserObject->phone;
        }

        return $response;
    }
}
