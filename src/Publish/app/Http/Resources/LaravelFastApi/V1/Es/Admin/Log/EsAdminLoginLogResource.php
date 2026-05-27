<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-25 22:55:20
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 02:13:26
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Log\EsAdminLoginLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Log;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsAdminLoginLogResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

    //禁止中文转 Unicode
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;

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

        $response = [
            'admin_login_log_uid' => $this->resource->admin_login_log_uid,
            'admin_uid' => $this->resource->admin_uid,
            'admin_account_name' => '',
            'admin_nikc_name' => '',
            'admin_real_name' => '',
            'admin_phone' => '',
            'data_type' => $this->resource->data_type,
            'login_type' => $this->resource->login_type,
            'status' => $this->resource->status,
            'instruction' => $this->resource->instruction,
            'ip' => $this->resource->ip,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];

        $adminIndexName = config('common_es.indices.user.admins');

        $esAdminObject = EsQueryFacade::index($adminIndexName)->where('admin_uid', $this->resource->admin_uid)->get()->first();

        if ($esAdminObject) {
            $response['admin_account_name'] = $esAdminObject->account_name;
            $response['admin_nick_name'] = $esAdminObject->nick_name;
            $response['admin_real_name'] = $esAdminObject->real_name;
            $response['admin_phone'] = $esAdminObject->phone;
        }

        return $response;
    }
}
