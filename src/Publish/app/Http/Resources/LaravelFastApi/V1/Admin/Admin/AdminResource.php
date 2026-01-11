<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-05 01:55:37
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Admin\AdminResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Resources\LaravelFastApi\V1\Admin\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\LaravelFastApi\V1\Admin\System\RoleResource;

class AdminResource extends JsonResource
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

		$role_id = json_decode($this->resource->role_id);
		$response = [
			'id'=>$this->resource->id,
			'user_id'=>$this->resource->user_id,
			'switch'=>$this->resource->switch,
			'account_name'=>$this->resource->account_name,
			'phone'=>$this->resource->phone,
			'created_at'=>$this->resource->created_at,
			'role_id'=>$role_id?$role_id:[]
		];

		if($this->resource->relationLoaded('user') && !is_null($this->resource->user))
		{
			$user = $this->resource->user;

			if($user->relationLoaded('userInfo') && !is_null($user->userInfo))
			{
				$userInfo = $user->userInfo;

				$response['nick_name'] = $userInfo->nick_name ?? '';
				$response['real_name'] = $userInfo->real_name ?? '';
				$response['solar_birthday_at'] = $userInfo->solar_birthday_at ?? '';
				$response['chinese_birthday_at'] = $userInfo->chinese_birthday_at ?? '';
				$response['sex'] = $userInfo->sex ?? '';
				$response['id_number'] = $userInfo->id_number ?? '';
				$response['introduction'] = $userInfo->introduction ?? '';
			}

			if($user->relationLoaded('userAvatar') && !is_null($user->userAvatar))
			{
				$userAvatar = $user->userAvatar->firstWhere('is_default',1);

				// 4. 校验默认头像是否存在
				if(!is_null($userAvatar) && $userAvatar->relationLoaded('albumPicture'))
				{
					$albumPicture = $userAvatar->albumPicture;
					// 5. 校验图片信息是否存在
					if(!is_null($albumPicture))
					{
						$response['avatar'] = asset('storage'.$albumPicture->picture_path.DIRECTORY_SEPARATOR.$albumPicture->picture_file);
					} else {
						$response['avatar'] = ''; // 无头像时返回空字符串
					}
				} else {
					$response['avatar'] = '';
				}

			}

			// 6. 校验role关联是否加载且存在
			if($user->relationLoaded('role') && !is_null($user->role))
			{
				$response['role'] = RoleResource::collection($user->role);
			} else {
				$response['role'] = []; // 无角色时返回空数组
			}

		}
		
		return $response;
    }


}
