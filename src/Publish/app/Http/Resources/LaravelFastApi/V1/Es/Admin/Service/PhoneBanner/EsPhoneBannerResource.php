<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-19 15:10:52
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 16:13:05
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\PhoneBanner\EsPhoneBannerResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\PhoneBanner;

use Illuminate\Http\Resources\Json\JsonResource;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;

class EsPhoneBannerResource extends JsonResource
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
            'album_picture_uid' => $this->resource->album_picture_uid,
            'picture' => '',
            'redirect_url' => $this->resource->redirect_url,
            'note' => $this->resource->note,
            'sort' => $this->resource->sort,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at
        ];

        //先处理图片
        $pictureIndexName = config('common_es.indices.album.album_pictures');

        if ($this->resource->album_picture_uid) {

            $esAlbumPictureObject = EsQueryFacade::index($pictureIndexName)->whereNull('deleted_at')->where('album_picture_uid', $this->resource->album_picture_uid)->get()->first();
            $esResult = EsFacade::findDoc($pictureIndexName, $this->resource->album_picture_uid);
            if (isset($esAlbumPictureObject) && $esAlbumPictureObject->album_picture_uid) {

                $picture_type = $esAlbumPictureObject->picture_type;
                $picture_path = $esAlbumPictureObject->picture_path;
                $picture_file = $esAlbumPictureObject->picture_file;

                if ($picture_type == 10) {
                    $picture = asset('storage'.$picture_path.DIRECTORY_SEPARATOR.$picture_file);
                }

                if ($picture_type == 20) {
                    $picture = $esAlbumPictureObject->picture_url;
                }

                $response['picture'] = $picture;


            }
        }


        return $response;
    }
}
