<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-24 21:07:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 16:38:06
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture\EsAlbumResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\Picture;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsAlbumResource extends JsonResource
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

        $response =  [
            'album_uid' => $this->resource->album_uid,
            'admin_uid' =>isset($this->resource->admin_uid)?$this->resource->admin_uid:0, 
            'user_uid'=>isset($this->resource->user_uid)?$this->resource->user_uid:0,
            'cover_album_picture_uid' => $this->resource->cover_album_picture_uid,
            'album_type' => $this->resource->album_type,
            'is_default' => $this->resource->is_default,
            'is_system' => isset($this->resource->is_system)?$this->resource->is_system:0,
            'album_name' => $this->resource->album_name,
            'album_description' => $this->resource->album_description,
            'created_at' => isset($this->resource->created_at)?$this->resource->created_at:'',
            'updated_at' => isset($this->resource->updated_at)?$this->resource->updated_at:'',
            'sort' => $this->resource->sort,
            'picture_number' => 0,
            'cover_album_picture' => ''
        ];

        $albumPictureIndexName = config('common_es.indices.album.album_pictures');

        //处理相册图片数量
        $albumPictureNumber = EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_uid', $this->resource->album_uid)->get()->count();

        if ($albumPictureNumber) {
            $response['picture_number'] = $albumPictureNumber;
        }

        if ($this->resource->cover_album_picture_uid) {
            //处理相册封面图片
            $coverAlbumPictureObject = EsQueryFacade::index($albumPictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $this->resource->cover_album_picture_uid)->get()->first();

            if ($coverAlbumPictureObject) {
                //判断图片类型
                if ($coverAlbumPictureObject->picture_type == 10) {
                    $response['cover_album_picture'] = asset('/storage'.$coverAlbumPictureObject->picture_path.DIRECTORY_SEPARATOR.$coverAlbumPictureObject->picture_file);
                }

                if ($coverAlbumPictureObject->picture_type == 20) {
                    $response['cover_album_picture'] = $coverAlbumPictureObject->picture_url;
                }
            }
        }




        return $response;
    }
}
