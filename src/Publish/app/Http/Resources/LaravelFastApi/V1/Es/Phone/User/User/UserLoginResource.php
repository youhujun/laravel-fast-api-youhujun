<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-30 18:22:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 15:44:03
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Phone\User\User\UserLoginResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Phone\User\User;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Models\LaravelFastApi\V1\User\Platform\UserWechatUnionid;
use App\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion;

class UserLoginResource extends JsonResource
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
            'token' => $this->resource->remember_token,
            'user_uid' => $this->resource->user_uid,
            'phone' => $this->resource->phone ?? null,
            'openid' => '',
            'unionid' => ''
        ];

        //先从es查询
        $openidIndexName = config('common_es.indices.union.user_system_wechat_config_unions');

        $esUserSystemWechatConfigUnionObject = EsQueryFacade::index($openidIndexName)->whereNull('deleted_at')->where('user_uid', $this->resource->user_uid)->get()->first();

        if (!isset($esUserSystemWechatConfigUnionObject->openid)) {
            $userSystemWechatConfigUnionObject = UserSystemWechatConfigUnion::queryByShard($this->resource->user_uid)->where('user_uid', $this->resource->user_uid)->first();

            if (isset($userSystemWechatConfigUnionObject->openid)) {
                $response['openid'] = $userSystemWechatConfigUnionObject->openid;
            }
        }

        //先从es查询
        $unionidIndexName = config('common_es.indices.union.user_wechat_unionids');

        $esUserWechatUnionidObject = EsQueryFacade::index($unionidIndexName)->whereNull('deleted_at')->where('user_uid', $this->resource->user_uid)->get()->first();

        if (!isset($esUserWechatUnionidObject->unionid)) {
            $userWechatUnionidObject = UserWechatUnionid::queryByShard($this->resource->user_uid)->where('user_uid', $this->resource->user_uid)->first();

            if (isset($userWechatUnionidObject->unionid)) {
                $response['unionid'] = $userSystemWechatConfigUnionObject->unionid;
            }
        }






        return $response;
    }
}
