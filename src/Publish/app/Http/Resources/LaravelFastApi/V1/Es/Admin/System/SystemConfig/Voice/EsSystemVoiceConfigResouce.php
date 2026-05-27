<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-16 23:24:46
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-05 13:01:33
 * @FilePath: \youhu-laravel-api-12\app\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Voice\EsSystemVoiceConfigResouce.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Es\Admin\System\SystemConfig\Voice;

use Illuminate\Http\Resources\Json\JsonResource;

class EsSystemVoiceConfigResouce extends JsonResource
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

        $voice_url = $this->resource->voice_url??'';
        $voice_path = $this->resource->voice_path??'';
        $voice_file = $this->resource->voice_file??'';
        $response = [
            'id' => $this->resource->id,
            'voice_title' => $this->resource->voice_title,
            'channle_name' => $this->resource->channle_name,
            'channle_event' => $this->resource->channle_event,
            'voice_save_type' => $this->resource->voice_save_type,
            'voice_url' => $voice_url,
            'voice_path' => $voice_path,
            'voice_file' => $voice_file,
            'note' => $this->resource->note,
            'created_at' => $this->resource->created_at,
            'sort' => $this->resource->sort,
        ];

        //本地存储
        if ($this->resource->voice_save_type == 10) {
            $response['voice_use_url'] = asset('/storage'.DIRECTORY_SEPARATOR.$voice_path);

            if ($voice_file) {
                $response['voice_use_url'] = asset('/storage'.DIRECTORY_SEPARATOR.$voice_path.DIRECTORY_SEPARATOR.$voice_file);
            }
        }

        //存储桶存储
        if ($this->resource->voice_save_type == 20) {
            $response['voice_use_url'] = $voice_url;
        }

        return $response;
    }
}
