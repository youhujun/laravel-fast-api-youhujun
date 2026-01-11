<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-16 12:55:39
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-22 13:28:35
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserInfoResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Resources\LaravelFastApi\V1\Phone\User\User;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\User\User;
use App\Models\User\Info\UserInfo;
use App\Models\User\Info\UserAvatar;

use App\Models\User\UserLevel;

use App\Models\Picture\AlbumPicture;


class UserInfoResource extends JsonResource
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
                'user_id'=>$this->resource->id,
                'phone'=>$this->resource->phone,
                'created_at'=>$this->resource->created_at,
				'level_name'=>'暂无',
				'avatar'=>'暂无',
				'sex_name'=>'未知',
				'sex'=>0
            ];

			//处理级别
			$userLevelObject = UserLevel::find($this->resource->level_id);

			if($userLevelObject)
			{
				$response['level_name'] = $userLevelObject->level_name;
			}

			//处理头像
			$userAvatarObject = UserAvatar::where('user_id',$this->resource->id)->orderBy('created_time','desc')->first();

			//p($userAvatarObject);die;

			if($userAvatarObject)
			{
				$albumPictureObject = AlbumPicture::find($userAvatarObject->album_picture_id);

				if($albumPictureObject)
				{
					if($albumPictureObject->picture_type == 20)
					{
						$response['avatar'] = $albumPictureObject->picture_url;
					}
					else
					{
						$response['avatar'] = asset('storage/'.$albumPictureObject->picture_path.$albumPictureObject->picture_file);
					}
					
				}
			}

			//处理性别
			$userInfoObject = UserInfo::where('user_id',$this->resource->id)->first();

			if($userInfoObject)
			{
				$response['sex'] = $userInfoObject->sex;

				$sexNameArray = [0=>'未知',10=>'男',20=>'女'];

				$response['sex_name'] = $sexNameArray[$userInfoObject->sex];
			}
        }
 
        return $response;
    }


}
