<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-30 09:28:17
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-25 12:35:44
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Help\LabelRecource.php
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\Help;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class LabelRecource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * 使用类型
     *
     * @var integer 10 文章关联使用
     */
    protected static $useType = 0;

    public static function setUseType($useType = 0)
    {
        self::$useType = $useType;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		$response = [
			'id'=>$this->resource->id,
			'sort'=>$this->resource->sort,
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'label_name'=>$this->resource->label_name,
			'label_code'=>$this->resource->label_code,
			'note'=>$this->resource->note,
		];

		if(isset($this->resource->children) && count($this->resource->children))
		{
			$response['children'] = $this->collection($this->resource->children);
		}

		if(isset($this->resource->label_picture_id) && $this->resource->label_picture_id)
		{
			$albumPictureObject = AlbumPicture::find($this->resource->label_picture_id);

				$response['picture'] = asset('storage'.$albumPictureObject->picture_path.'/'.$albumPictureObject->picture_file);
		}

		if(self::$useType == 10)
		{
			$response = [
				'label_id'=>$this->resource->id,
				'label_name'=>$this->resource->label_name,
				'label_code'=>$this->resource->label_code,
			];
		}
        
        return $response;
    }


}
