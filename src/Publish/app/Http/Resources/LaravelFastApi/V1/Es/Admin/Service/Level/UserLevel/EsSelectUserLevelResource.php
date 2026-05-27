<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-19 14:41:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 12:35:51
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel\EsSelectUserLevelResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Facades\Common\V1\Es\EsQueryFacade;

class EsSelectUserLevelResource extends JsonResource
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
            'id' => $this->resource->id,
            'level_name' => $this->resource->level_name,
            'level_code' => $this->resource->level_code,
            'amount' => $this->resource->amount,
            'background_picture_uid' => $this->resource->background_picture_uid ?? 0,
            //真正的图片地址
            'background_picture' => '',
            'note' => $this->resource->note,
            'sort' => $this->resource->sort,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];

        //先处理图片
        $pictureIndexName = config('common_es.indices.album.album_pictures');

        if ($this->resource->background_picture_uid) {

            $esAlbumPictureObject = EsQueryFacade::index($pictureIndexName)->where('album_picture_uid', $this->resource->background_picture_uid)->get()->first();

            if(isset($esAlbumPictureObject->album_picture_uid)){
                $picture_type = $esAlbumPictureObject->picture_type;
                $picture_path = $esAlbumPictureObject->picture_path;
                $picture_file = $esAlbumPictureObject->picture_file;

                if($picture_type == 10){
                    $picture = asset('storage'.$picture_path.DIRECTORY_SEPARATOR.$picture_file);
                }

                if($picture_type == 20){
                    $picture = $esAlbumPictureObject->picture_url;

                }

                $response['background_picture'] = $picture;

            }
        }


        return $response;
    }
}
