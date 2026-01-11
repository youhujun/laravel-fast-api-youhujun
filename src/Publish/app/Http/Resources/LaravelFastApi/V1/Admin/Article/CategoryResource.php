<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 22:07:22
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-25 11:59:19
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Article\CategoryResource.php
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\Article;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

class CategoryResource extends JsonResource
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
			'revision'=>$this->resource->revision,
			'created_at'=>$this->resource->created_at,
			'switch'=>$this->resource->switch,
			'sort'=>$this->resource->sort,
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'rate'=>\bcmul($this->resource->rate,100,0),
			'category_name'=>$this->resource->category_name,
			'category_code'=>$this->resource->category_code,
			'category_picture_id'=>$this->resource->category_picture_id,
			'note'=>$this->resource->note,
        ];

		if(self::$useType == 10)
		{
			$response = [
				'category_id'=>$this->resource->id,
				'category_name'=>$this->resource->category_name,
				'category_code'=>$this->resource->category_code
			];
		}

		if(isset($this->resource->children) && count($this->resource->children))
		{
			$response['children'] = $this->collection($this->resource->children);
		}

		if(isset($this->resource->category_picture_id) && $this->resource->category_picture_id)
		{
			$albumPictureObject = AlbumPicture::find($this->resource->category_picture_id);

			$response['picture'] = asset('storage'.$albumPictureObject->picture_path.'/'.$albumPictureObject->picture_file);
		}
      
        return $response;
    }


}
