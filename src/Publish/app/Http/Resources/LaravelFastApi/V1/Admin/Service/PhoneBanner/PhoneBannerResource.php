<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-25 09:47:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 16:24:28
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Service\PhoneBanner\PhoneBannerResource.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\Service\PhoneBanner;

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
            'id'=>$this->resource->id,
            'user_id'=>$this->resource->user_id,
            'album_picture_id'=>$this->resource->album_picture_id,
            'redirect_url'=>$this->resource->redirect_url,
            'note'=>$this->resource->note,
            'sort'=>$this->resource->sort,
            'created_at'=>$this->resource->created_at,
            'updated_at'=>$this->resource->updated_at
        ];

		if($this->resource->album_picture_id)
		{
			$AlbumPictureObject = AlbumPicture::where('id',$this->resource->album_picture_id)->first();

			if($AlbumPictureObject)
			{
				$response['picture'] = asset('storage'.$AlbumPictureObject->picture_path.DIRECTORY_SEPARATOR.$AlbumPictureObject->picture_file);
			}
		}
		
        return $response;

    }

}
