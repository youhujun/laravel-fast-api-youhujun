<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-18 16:46:43
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-10 12:31:03
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel\EsUserLevelResource.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\Service\Level\UserLevel;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;


class EsUserLevelResource extends JsonResource
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
            //级别对应的级别配置项
            'level_item_array' => [],
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

        $userLevelItemUnionIndexName = config('common_es.indices.union.user_level_item_unions');

        $userLevelItemUnionEsQeury = EsQueryFacade::index($userLevelItemUnionIndexName);

        $userLevelItemUnionEsQeury->whereNull('deleted_at');

        $userLevelItemUnionEsQeury->where('user_level_id', $this->resource->id);

        $userLevelItemUnionCollection = $userLevelItemUnionEsQeury->limit(1000)->get();

        $level_item_id_array = $userLevelItemUnionCollection->pluck('level_item_id')->keyBy('user_level_id')->toArray();

        //定义容器
        $level_item_array = [];

        $levelItemIndexName = config('common_es.indices.business.level_items');

        foreach ($userLevelItemUnionCollection as $key => $userLevelItemUnionObject) {
            $levelItemEsQuery = EsQueryFacade::index($levelItemIndexName);

            $levelItemEsQuery->where('id', $userLevelItemUnionObject->level_item_id);

            $levelItemObject = $levelItemEsQuery->get()->first();

            $single_item_array = [];
            $single_item_array['user_level_item_unon_id'] = $userLevelItemUnionObject?->id;
            $single_item_array['level_item_id'] = $levelItemObject?->id ? $levelItemObject?->id : 0;
            $single_item_array['item_name'] = $levelItemObject?->item_name ? $levelItemObject->item_name : '';
            $single_item_array['item_code'] = $levelItemObject?->item_code ? $levelItemObject->item_code : '';
            $single_item_array['description'] = $levelItemObject?->description ? $levelItemObject->description : '';
            $single_item_array['value_type'] =  $userLevelItemUnionObject?->value_type ? $userLevelItemUnionObject->value_type : 0;
            $single_item_array['value'] =  $userLevelItemUnionObject?->value ? $userLevelItemUnionObject->value : null;

            $level_item_array[] = $single_item_array;
        }

        $response['level_item_array'] = $level_item_array;


        return $response;
    }
}
