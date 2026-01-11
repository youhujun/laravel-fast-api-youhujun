<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-25 09:47:12
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-16 12:22:16
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\System\Permission\PermissionResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\System\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\System\Role\Role;

class PermissionResource extends JsonResource
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
		//p($this->resource);die;
		$response = [
			'id'=>$this->resource->id,
			'parent_id'=>$this->resource->parent_id,
			'deep'=>$this->resource->deep,
			'name'=>$this->resource->route_name,
			'path'=>$this->resource->route_path,
			'component'=>$this->resource->component
		];

		//查询权限的角色
		$rolesLogicNameArray = [];

		$rolesIdCollection = RolePermissionUnion::where('permission_id',$this->resource->id)->select('role_id')->get();

		foreach ($rolesIdCollection as $key => $item) 
		{
			$roleObject = Role::find($item->role_id);

			if($roleObject?->logic_name)
			{
				$rolesLogicNameArray[] = $roleObject?->logic_name;
			}
			
		}

		$response['meta'] = [
			'title'=>$this->resource->meta_title,
			'icon'=>$this->resource->meta_icon,
			'hidden'=>$this->resource->hidden?true:false,
			'keepAlive'=>$this->resource->meta_no_cache?true:false,
			'alwaysShow'=>$this->resource->always_show?true:false,
			'roles'=>$rolesLogicNameArray
		];

		//如果有跳转路径才加载,没有就不加
	    if(trim($this->resource->redirect) && !empty($this->resource->redirect))
		{
			$response['redirect'] = $this->resource->redirect;
		}

		if(isset($this->resource->children) && count($this->resource->children))
		{
			//p($this->resource['children']);
			$response['children'] = $this->collection($this->resource->children);
		}

        return $response;
    }


}
