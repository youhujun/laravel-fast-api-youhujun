<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-24 21:07:59
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 01:59:07
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumPictureResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture;

use Illuminate\Http\Resources\Json\JsonResource;

class EsAlbumPictureResource extends JsonResource
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
     * 只显示图片路径
     *
     * @var boolean
     */
    public static $onlyShowPicture = false;

    public static function onlyShowPictureUrl()
    {
        self::$onlyShowPicture = true;
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

        $picture = '';
        //处理图片
        if ($this->resource->picture_type == 10) {
            $picture = asset('storage'.$this->resource->picture_path.DIRECTORY_SEPARATOR.$this->resource->picture_file);
        }

        if ($this->resource->picture_type == 20) {
            $picture = $this->resource->picture_url;
        }


        if (self::$onlyShowPicture) {
            $response = [
                'picture' => $picture
            ];
        } else {
            $response = [
                'album_picture_uid' => (int)$this->resource->album_picture_uid,
                'user_uid' => (int)$this->resource->user_uid,
                'album_uid' => (int)$this->resource->album_uid,
                'created_at' => $this->resource->created_at,
                'updated_at' => $this->resource->updated_at,
                'picture_name' => $this->resource->picture_name,
                'picture_path' => $this->resource->picture_path,
                'picture_file' => $this->resource->picture_file,
                'picture_size' => $this->resource->picture_size,
                'picture_spec' => $this->resource->picture_spec,
                'picture_url' => $this->resource->picture_url,
                'picture_type' => $this->resource->picture_type,
                'picture' => $picture
            ];
        }

        return $response;
    }
}
