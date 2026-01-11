<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-04-07 12:03:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-08-31 17:39:52
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\RegionResource.php
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\System;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * 控制是否显示详情
     *
     * @var [type]
     */
    public static $showInfo ;

    public static function showControl($showInfo = 0)
    {
        self::$showInfo = $showInfo;
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
        
		//P($this->resource);die;

		$response = [
			'id'=>$this->resource->id,
			'region_name'=>$this->resource->region_name,
        ];

		if(self::$showInfo)
        {
			$response['parent_id'] = $this->resource->parent_id;
			$response['deep'] = $this->resource->deep;
			$response['region_area'] = $this->resource->region_area;
			$response['created_at'] = $this->resource->created_at;
			$response['sort'] = $this->resource->sort;
		}

		if(isset($this->resource->children) && count($this->resource->children))
		{
			//p($this->resource['children']);
			$response['children'] = $this->collection($this->resource->children);
		}

        return $response;
    }


}
