<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-09-09 13:25:29
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-09-26 16:32:29
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\SingleMenuFormResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleMenuFormResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

    // public static $replaceType;

    // public static function setReplaceType($replaceType = 10)
    // {
    //     self::$replaceType = $replaceType;
    // }

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
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'type'=>$this->resource->type,
			'route_name'=>$this->resource->route_name,
			'route_path'=>$this->resource->route_path,
			'component'=>$this->resource->component,
			'hidden'=>$this->resource->hidden,
			'always_show'=>$this->resource->always_show,
			'perm'=>$this->resource->permission_tag,
			'switch'=>$this->resource->switch,
			'sort'=>$this->resource->sort,
			'icon'=>$this->resource->meta_icon,
			'title'=>$this->resource->meta_title,
			'cache'=>$this->resource->meta_no_cache,
			'affix'=>$this->resource->meta_affix,
			'breadcrumb'=>$this->resource->meta_breadcrumb,
			'active_menu'=>$this->resource->meta_active_menu,
		];

		//如果有跳转路径才加载,没有就不加
	    if(trim($this->resource->redirect) && !empty($this->resource->redirect))
		{
			$response['redirect'] = $this->resource->redirect;
		}

        return $response;
    }


}
