<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-21 00:47:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 00:49:07
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Admin\EsAdminResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Admin;

use App\Facades\Common\V1\Es\EsQueryFacade;
use Illuminate\Http\Resources\Json\JsonResource;

class EsAdminResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;


    //禁止中文转 Unicode
    protected $encodingOptions = JSON_UNESCAPED_UNICODE;

    //通用控制类型
    public static $showControlType;

    public static function SetShowControlType($showControlType = 10)
    {
        self::$showControlType = $showControlType;
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
            'admin_uid' => $this->resource->admin_uid ?? 0,
            'user_uid' => $this->resource->user_uid,
            'phone' => $this->resource->phone,
            'account_name' => $this->resource->account_name,
            'account_status' => $this->resource->account_status,
            'id_number' => $this->resource->id_number,
            'nick_name' => $this->resource->nick_name,
            'real_name' => $this->resource->real_name,
            'solar_birthday_at' => $this->resource->solar_birthday_at,
            'chinese_birthday_at' => $this->resource->chinese_birthday_at,
            'sex' => $this->resource->sex,
            'introduction' => $this->resource->introduction,
            'ablum_uid' => $this->resource->ablum_uid,
            'avatar' => $this->resource->avatar,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'role_cascader_id_array' => [],
             'role_name_array'=>get_es_user_roles($this->resource->user_uid)
        ];

        if (self::$showControlType == 10) {
            if(isset($this->resource->role_cascader_json)){

                $role_cascader_id_array = json_decode($this->resource->role_cascader_json, true);
                $response['role_cascader_id_array'] = $role_cascader_id_array;
            }
        }
        return $response;
    }
}
