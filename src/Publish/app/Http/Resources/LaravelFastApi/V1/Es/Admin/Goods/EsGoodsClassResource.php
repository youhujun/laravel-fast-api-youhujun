<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-20 08:49:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 12:34:37
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Goods\EsGoodsClassResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Goods;

use Illuminate\Http\Resources\Json\JsonResource;

use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

class EsGoodsClassResource extends JsonResource
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
            'id' => $this->resource->id,
            'parent_id' => $this->resource->parent_id,
            'deep' => $this->resource->deep,
            'switch' => $this->resource->switch,
            'rate' => $this->resource->rate,
            'goods_class_name' => $this->resource->goods_class_name,
            'goods_class_code' => $this->resource->goods_class_code,
            'goods_class_picture_uid' => $this->resource->goods_class_picture_uid,
            'picture' => '',
            'is_certificate' => $this->resource->is_certificate,
            'certificate_number' => $this->resource->certificate_number,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at,
            'sort' => $this->resource->sort
        ];

        if (isset($this->resource->children) && count($this->resource->children)) {
            //p($this->resource['children']);
            $response['children'] = $this->collection($this->resource->children);
        }

        if ($this->resource->goods_class_picture_uid) {
            $pictureIndexName = config('common_es.indices.album.album_pictures');

            $esResult = EsFacade::findDoc($pictureIndexName, $this->resource->goods_class_picture_uid);
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
