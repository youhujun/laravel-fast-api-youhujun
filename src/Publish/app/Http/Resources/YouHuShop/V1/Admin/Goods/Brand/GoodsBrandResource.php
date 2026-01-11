<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-21 05:29:28
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-03 10:30:49
 * @FilePath: \app\Http\Resources\YouHuShop\V1\Admin\Goods\Brand\GoodsBrandResource.php
 */

namespace App\Http\Resources\YouHuShop\V1\Admin\Goods\Brand;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class GoodsBrandResource extends JsonResource
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
                'shop_id'=>$this->resource->shop_id,
                'server_id'=>$this->resource->server_id,
                'album_picture_id'=>$this->resource->album_picture_id,
                'barnd_type'=>$this->resource->barnd_type,
                'brand_name'=>$this->resource->brand_name,
                'brand_code'=>$this->resource->brand_code,
                'note'=>$this->resource->note,
                'created_at'=>$this->resource->created_at,
                'sort'=>$this->resource->sort
            ];

			$logoPicture = AlbumPicture::find($this->resource->album_picture_id);

			if($logoPicture)
			{
				$response['logo'] = asset('storage'.$logoPicture->picture_path.'/'.$logoPicture->picture_file);
			}

        }

        return $response;
    }


}
