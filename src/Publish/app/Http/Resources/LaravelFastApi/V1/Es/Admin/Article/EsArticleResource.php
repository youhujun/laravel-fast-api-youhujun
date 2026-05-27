<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-22 19:03:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 21:41:24
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Article\EsArticleResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Article;

use App\Facades\Common\V1\Es\EsQueryFacade;
use Illuminate\Http\Resources\Json\JsonResource;

use function Symfony\Component\Translation\t;

class EsArticleResource extends JsonResource
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

        $category_cascader_id_array = json_decode($this->resource->category_cascader_json, true);
        $label_cascader_id_array = json_decode($this->resource->label_cascader_json, true);
        $category_id_array = get_cascader_array($category_cascader_id_array);

        $response = [
            'article_uid' => $this->resource->id,
            'admin_uid' => $this->resource->admin_uid,
            'user_uid' => $this->resource->user_uid,
            'title' => $this->resource->title,
            'status' => $this->resource->status,
            'type' => $this->resource->type,
            'is_top' => $this->resource->is_top,
            'check_status' => $this->resource->check_status,
            'published_at' => $this->resource->published_at,
            'checked_at' => $this->resource->checked_at,
            'article_info' => htmlspecialchars_decode($this->resource->article_info),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'category_cascader_id_array' =>$category_cascader_id_array,
            'label_cascader_id_array' =>$label_cascader_id_array,
            'sort' => $this->resource->sort,
            'category_name_array' => []
        ];

        $adminIndexName = config('common_es.indices.user.admins');
        $userIndexName = config('common_es.indices.user.users');
        $categoryIndexName = config('common_es.indices.business.article_categories');
        $labelIndexName = config('common_es.indices.business.labels');

        if ($this->resource->admin_uid) {
            $esAdminObject = EsQueryFacade::index($adminIndexName)->whereNull('deleted_at')->where('admin_uid', $this->resource->admin_uid)->get()->first();

            $response['admin_name'] = $esAdminObject->nick_name ?? $esAdminObject->real_name;
        }

        $esUserObject = EsQueryFacade::index($userIndexName)->whereNull('deleted_at')->where('user_uid', $this->resource->user_uid)->get()->first();

        $response['user_name'] = $esUserObject->nick_name ?? $esUserObject->real_name;

        if (count($category_id_array)) {
            $select_category_id_array = collect($category_id_array)->flatten()->unique()->values()->all();
            ;
            $categoryNameArray = EsQueryFacade::index($categoryIndexName)->whereNull('deleted_at')->whereIn('id', $select_category_id_array)->limit(1000)->get()->pluck('category_name')->toArray();

            $response['category_name_array'] = $categoryNameArray;
        }

        return $response;
    }
}
