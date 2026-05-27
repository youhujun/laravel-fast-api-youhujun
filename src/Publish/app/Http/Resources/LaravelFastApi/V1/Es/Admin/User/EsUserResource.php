<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-27 12:56:22
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 17:45:20
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\User\EsUserResource.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class EsUserResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

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
            'shard_db' => $this->resource->shard_db,
            'shard_table' => $this->resource->shard_table,
            'user_uid' => $this->resource->user_uid,
            'source_user_uid' => $this->resource->source_user_uid,
            'parent_user_uid' => $this->resource->parent_user_uid,
            'phone' => $this->resource->phone,
            'account_name' => $this->resource->account_name,
            'account_status' => $this->resource->account_status,
            'invite_code' => $this->resource->invite_code,
            'real_auth_status' => $this->resource->real_auth_status,
            'level_id' => $this->resource->level_id,
            'user_level'=>get_user_level($this->resource->user_uid),
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
            'deleted_at' => $this->resource->deleted_at,
            'role_cascader_id_array' => [],
            'role_name_array'=>get_es_user_roles($this->resource->user_uid)
        ];

         if(isset($this->resource->role_cascader_json)){

            $role_cascader_id_array = json_decode($this->resource->role_cascader_json, true);
            $response['role_cascader_id_array'] = $role_cascader_id_array;
        }




        return $response;
    }
}
