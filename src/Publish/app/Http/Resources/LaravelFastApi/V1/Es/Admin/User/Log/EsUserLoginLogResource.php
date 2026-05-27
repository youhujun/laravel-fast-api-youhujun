<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-26 04:57:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 05:13:55
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log\EsUserLoginLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\Log;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Facades\Common\V1\Es\EsQueryFacade;

class EsUserLoginLogResource extends JsonResource
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
            'user_login_log_uid' => $this->resource->user_login_log_uid,
            'user_uid' => $this->resource->user_uid,
            'user_account_name' => '',
            'user_nikc_name' => '',
            'user_real_name' => '',
            'user_phone' => '',
            'data_type' => $this->resource->data_type,
            'login_type' => $this->resource->login_type,
            'status' => $this->resource->status,
            'instruction' => $this->resource->instruction,
            'ip' => $this->resource->ip,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];

        $userIndexName = config('common_es.indices.user.users');

        $esUserObject = EsQueryFacade::index($userIndexName)->where('user_uid', $this->resource->user_uid)->get()->first();

        if ($esAdminObject) {
            $response['user_account_name'] = $esUserObject->account_name;
            $response['user_nick_name'] = $esUserObject->nick_name;
            $response['user_real_name'] = $esUserObject->real_name;
            $response['user_phone'] = $esUserObject->phone;
        }

        return $response;


        return $response;
    }
}
