<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-13 17:10:35
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-14 09:34:25
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserRealAuth\EsUserRealAuthLogResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User\UserRealAuth;

use Illuminate\Http\Resources\Json\JsonResource;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class EsUserRealAuthLogResource extends JsonResource
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

        $indexName = config('common_es.indices.user.users');
        $adminIndexName = config('common_es.indices.user.admins');

        $response = [
            'user_real_auth_log_uid' => $this->resource->user_real_auth_log_uid,
            'user_uid' => $this->resource->user_uid,
            'admin_uid' => $this->resource->admin_uid,
            'data_type' => $this->resource->data_type,
            'status' => $this->resource->status,
            'auth_apply_at' => $this->resource->auth_apply_at,
            'auth_at' => $this->resource->auth_at,
            'refuse_info' => $this->resource->refuse_info,
            'sort' => $this->resource->sort,
        ];

        $esUserArray = [];
        $esResult = EsFacade::findDoc($indexName, $this->resource->user_uid);

        if (isset($esResult['code']) && $esResult['code'] == 0) {
            $esUserArray = $esResult['data'];
        }

        if (isset($esUserArray['nick_name'])) {
            $response['nick_name'] = $esUserArray['nick_name'];
        }

        if (isset($esUserArray['real_name'])) {
            $response['real_name'] = $esUserArray['real_name'];
        }

        if (isset($esUserArray['account_name'])) {
            $response['user_account_name'] = $esUserArray['account_name'];
        }

        if (isset($esUserArray['phone'])) {
            $response['user_phone'] = $esUserArray['phone'];
        }

        $esAminArray = [];
        $esAdminResult = EsFacade::findDoc($adminIndexName, $this->resource->admin_uid);

        if (isset($esAdminResult['code']) && $esAdminResult['code'] == 0) {
            $esAminArray = $esAdminResult['data'];
        }

        if (isset($esAminArray['account_name'])) {
            $response['admin_account_name'] = $esAminArray['account_name'];
        }

        if (isset($esAminArray['phone'])) {
            $response['admin_phone'] = $esAminArray['phone'];
        }



        return $response;
    }
}
