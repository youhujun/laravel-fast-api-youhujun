<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 16:53:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 12:27:59
 * @FilePath: \app\Http\Resources\LaravelFastApi\V1\Admin\Login\AdminInfoResource.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Resources\LaravelFastApi\V1\Admin\Login;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


use  App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use  App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use  App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use  App\Models\LaravelFastApi\V1\Picture\Album;

class AdminInfoResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合原始键。
     *
     * @var bool
     */
    public $preserveKeys = true;

	public $codeArray;
	/**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource,$codeArray)
    {
        parent::__construct($resource);

        $this->codeArray = $codeArray;
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
		//p($this->resource);die;
		
		$responseData = [
			'userId'=>$this->resource->userId,
			'user_id'=>$this->resource->user_id,
			'admin_id'=>$this->resource->id,
			'username'=>'',
			'nickname'=>'',
			//'introduction'=>'',
			'roles'=>getAdminRoles($this->resource),
			'avatar'=>'',
			'phone'=>$this->resource->phone?$this->resource->phone:'',
			'perms'=>[]
		];

		//先检测redis 缓存有没有数据 如果有redis缓存数据
		$redisData = Redis::hget("admin_info:admin_info",$this->resource->id);

		if(isset($redisData) && !empty($redisData))
		{
			$adminInfoData = \json_decode($redisData,true);

			$responseData = $adminInfoData;
		} 

		//走到这里说明没redis缓存中没有
		if (!isset($adminInfoData) || empty($adminInfoData))
		{
			$user_id = $this->resource->user_id;

			$userInfoObject = UserInfo::where('user_id',$user_id)->first();

			if($userInfoObject)
			{
				$responseData['username'] = $userInfoObject->nick_name;
				$responseData['nickname'] = $userInfoObject->nick_name;
				//$responseData['introduction'] = $userInfoObject->introduction;
			}

			$avatar = $this->getAdminAvatar($user_id);

			if($avatar)
			{
				$responseData['avatar'] = $avatar;
			}

			//处理相册id
			$responseData['album_id'] = 1;

			$admin_id = $this->resource->id;

			$albumObject = Album::where('admin_id',$admin_id)->first();

			if($albumObject){
				$responseData['album_id'] = $albumObject->id;
			}

			$redisResult = Redis::hset("admin_info:admin_info",$this->resource->id,json_encode($responseData));

			if(!$redisResult)
			{
				Log::debug(['SaveRedisAdminInfoError'=>"redis存储管理员信息失败:{$admin->id}"]);
			}
		}

		$response = code($this->codeArray,['data'=>$responseData]);

        return $response;
    }

	

    /**
     * 获取管理员头像
     *
     * @param User $user
     * @return void
     */
    private function getAdminAvatar($user_id)
    {
        //远程连接
        $avatar = null;

		$adminAvatarObject = UserAvatar::where('user_id',$user_id)->where('is_default',1)->first();


        if($adminAvatarObject)
        {
			$albumPictureObject = AlbumPicture::find($adminAvatarObject->album_picture_id);

			//有图片就用管理员头像
            if($albumPictureObject)
            {
				//默认先用存储再服务器头像
                $avatar = asset('storage' . $albumPictureObject->picture_path .DIRECTORY_SEPARATOR. $albumPictureObject->picture_file);

                //存储方式是存储桶就换成存储桶
                if($albumPictureObject->picture_type == 20)
                {
                    $avatar = $albumPictureObject->picture_url;
                }

            }
        }

        //如果没有头像,就是使用默认头像
        if (!$avatar)
        {
            $avatar = $this->getAllUserAvatar();
        }

        return  $avatar;
    }

	/**
     * 获取所有用户的默认头像 1
     *
     * @return void
     */
    private function getAllUserAvatar()
    {

        $avatar = null;

        $albumPictureObject = AlbumPicture::find(2);

        if($albumPictureObject)
        {
            $avatar = asset('storage' . $albumPictureObject->picture_path .DIRECTORY_SEPARATOR. $albumPictureObject->picture_file);

            if($albumPictureObject->picture_type == 20 )
            {
                $avatar = $albumPictureObject->picture_url;
            }
        }

        return $avatar;
    }


}
