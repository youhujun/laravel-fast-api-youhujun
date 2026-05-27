<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-04-29 15:16:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 18:53:22
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Pub\System\PhoneBanner\PhoneBannerResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Pub\System\PhoneBanner;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class PhoneBannerResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    public static $replaceType;

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

       $response = [];

        if(\is_object($this->resource))
        {
            $response = [
                'id'=>$this->resource->id,
                'admin_id'=>$this->resource->admin_id,
                'album_picture_id'=>$this->resource->album_picture_id,
                'redirect_url'=>$this->resource->redirect_url,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort,
            ];

			$picture = AlbumPicture::find($this->resource->album_picture_id);

			$picture_url = null;

			if($picture->picture_type == 10)
			{
				$picture_url = asset('storage'.$picture->picture_path.'/'.$picture->picture_file);
			}
			else
			{
				$picture_url = $picture->picture_url;
			}

			$response['picture_url'] = $picture_url;
        }

        return $response;
    }


}
