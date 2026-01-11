<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-14 08:38:19
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-15 16:44:55
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\Role\RoleResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\Role;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\PermissionResource;

use App\Models\LaravelFastApi\V1\System\Role\Role;

class RoleResource extends JsonResource
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
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'switch'=>$this->resource->switch,
			'role_name'=>$this->resource->role_name,
			'logic_name'=>$this->resource->logic_name,
			/*  'created_at'=>$this->resource->created_at,
			'updated_at'=>$this->resource->updated_at, */
			'sort'=>$this->resource->sort,
		];

		if(isset($this->resource->children) && count($this->resource->children))
		{
			//p($this->resource['children']);
			$response['children'] = $this->collection($this->resource->children);
		}

		if($this->resource instanceof Role)
		{
			if($this->resource->relationLoaded('permission'))
			{
				if(isset($this->resource->permission) && count($this->resource->permission))
				{
					//p($this->resource['permission']);
					$response['permission'] = $this->resource->permission;
				}
			}
		}
        

        return $response;
	}
    


}
