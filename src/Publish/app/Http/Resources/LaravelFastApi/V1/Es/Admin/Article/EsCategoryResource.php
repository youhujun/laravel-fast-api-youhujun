<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-20 08:49:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 01:35:00
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Article\EsCategoryResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Article;

use Illuminate\Http\Resources\Json\JsonResource;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class EsCategoryResource extends JsonResource
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
     * 使用类型
     *
     * @var integer 10 文章关联使用
     */
    protected static $useType = 0;

    public static function setUseType($useType = 0)
    {
        self::$useType = $useType;
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
            'id' => $this->resource->id,
            'created_at' => $this->resource->created_at,
            'switch' => $this->resource->switch,
            'sort' => $this->resource->sort,
            'parent_id' => $this->resource->parent_id,
            'deep' => $this->resource->deep,
            'rate' => \bcmul($this->resource->rate, 100, 0),
            'category_name' => $this->resource->category_name,
            'category_code' => $this->resource->category_code,
            'category_picture_uid' => $this->resource->category_picture_uid,
            'picture' => '',
            'note' => $this->resource->note,
        ];

        if (self::$useType == 10) {
            $response = [
                'category_id' => $this->resource->id,
                'category_name' => $this->resource->category_name,
                'category_code' => $this->resource->category_code
            ];
        }

        if (isset($this->resource->children) && count($this->resource->children)) {
            $response['children'] = $this->collection($this->resource->children);
        }

        if ($this->resource->category_picture_uid) {
            $pictureIndexName = config('common_es.indices.album.album_pictures');

            $esResult = EsFacade::findDoc($pictureIndexName, $this->resource->category_picture_uid);
            if (isset($esResult['code']) && $esResult['code'] == 0) {
                $esPictureArray = $esResult['data'];

                if (isset($esPictureArray['picture_type'])) {
                    $picture_type = $esPictureArray['picture_type'];
                    if ($picture_type == 10) {
                        $picture = asset('storage'.$esPictureArray['picture_path'].DIRECTORY_SEPARATOR.$esPictureArray['picture_file']);
                    }

                    if ($picture_type == 20) {
                        $picture = $esPictureArray['picture_url'];
                    }

                    $response['picture'] = $picture;
                }
            }
        }

        return $response;
    }
}
