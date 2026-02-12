<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 15:44:57
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 12:01:03
 * @FilePath: \App\Services\Facade\Common\V1\User\User\CommonUserFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;


use App\Exceptions\Common\CommonException;

//注册用户(添加用户)
use App\Events\Common\V1\User\User\CommonUserRegisterEvent;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion;

use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Facades\Common\V1\User\User\CommonUserFacade
 */
class CommonUserFacadeService
{
   /* public function test()
   {
       echo "CommonUserFacadeService test";
   } */

  /**
	* 注册用户
	*
	* @param  [array] $param 传递的参数
	* @param  [object] $user 可选参数用户对象,可以从外部注册用户,然后传递过来,如果不传递,就在这里注册用户
	* @return [object] $user 返回注册成功的用户对象
	*/
   public function registerUser($param = [],$user = null)
   {
		//执行注册
		DB::beginTransaction();

		//如果没有从外面传入用户,就最简注册
		if(!isset($user) || empty($user))
		{
			$user = new User;

			//默认密码
			$user->password = Hash::make('abc123');

			$user->userId = Str::uuid()->toString();

			//用户级别最低
			$user->level_id = 1;

			//用户未实名认证
			$user->real_auth_status = 10;

			$user->switch = 1;

			$user->created_at = time();

			$user->created_time = time();

			$user->account_name = \bin2hex(\random_bytes(4));

			$user->auth_token = Str::random(20);

			if(isset($param['source']))
			{
				['source'=>$source] = $param;
				//10 H5 20 抖音小程序 30抖音小游戏 40微信公众号 50微信小程序 60微信小游戏
				$user->source =  $source;
			}

			$userResult = $user->save();

			// 邀请码
			$user_id = $user->id;

			if(mb_strlen($user_id) < 4)
			{
				$user->invite_code = str_pad($user_id,4,'0',STR_PAD_LEFT);
			}
			else
			{
				$user->invite_code = $user_id;
			}

			$userResult = $user->save();

			if(!$userResult)
			{
				DB::rollBack();
				throw new CommonException('AddUserError');
			}
		}

		//这里用来控制系统配置暂时写死,后续可以完善
		
		$param = ['user'=>$user,'validated'=>$param,'isTransation'=>1];

		CommonUserRegisterEvent::dispatch($param);

        //提交
        DB::commit();

		return $user;
   }

   /**
    * 获取用户头像
    *
    * 此方法根据用户 ID 获取用户的头像。如果用户有自定义头像，则返回该头像的 URL；
    * 否则返回系统默认头像。头像的获取顺序为：首先查找用户头像记录，
    * 然后根据相册图片 ID 查找相册图片，最后根据图片类型决定返回的头像 URL。
    *
    * @param object $user 用户对象，包含用户 ID。
    * @return string|null 返回用户头像的 URL，若未找到则返回 null。
    */
   public function getUserAvatar($user)
   {
		$userAvatarObject = UserAvatar::where('user_id',$user->id)->orderBy('created_time','desc')->first();

		$albumPictureObject = null;

		$avatar = null;

		if($userAvatarObject && isset($userAvatarObject?->album_picture_id))
		{
			$albumPictureObject = AlbumPicture::find($userAvatarObject->album_picture_id);

			if($albumPictureObject && isset($albumPictureObject?->picture_type))
			{
				if($albumPictureObject?->picture_type != 10)
				{
					$avatar = $albumPictureObject->picture_url;
				}
				else
				{
					$avatar = asset('/storage'.$albumPictureObject->picture_path.$albumPictureObject->picture_file);
				}
				
			}
		}
		else
		{
			$avatar = $this->getUserSysatemAvatar();
		}

		return $avatar;
   }

   /**
    * 获取用户系统头像
    *
    * 此方法从相册中查找用户头像。如果未找到头像，则抛出异常。
    * 如果找到的头像类型不是 10，则返回头像的 URL；否则，返回存储路径的完整 URL。
    *
    * @return string|null 用户头像的 URL，若未找到则返回 null
    * @throws CommonException 当未找到相册图片对象时抛出
    */
   private function getUserSysatemAvatar()
   {
		$avatar = null;

		$albumPictureObject = AlbumPicture::find(2);

		if(!$albumPictureObject)
		{
			throw new CommonException('UserSystemAvatarError');
		}

		if($albumPictureObject?->picture_type != 10)
		{
			$avatar = $albumPictureObject->picture_url;
		}
		else
		{
			$avatar = asset('/storage'.$albumPictureObject->picture_path.$albumPictureObject->picture_file);
		}

		return $avatar;
   }

   /**
    * 获取用户的 OpenID
    *
    * @param object $user 用户对象，包含用户的基本信息
    * @return string|null 返回用户的 OpenID，如果未找到则返回 null
    */
   public function getUserOpenid($user,$openid_type = 10)
   {
		$openid = null;

		$query = null; 

		$userWechatOfficialObject = null;

		//微信
		if(in_array($openid_type,[10,20,30]))
		{
			$query = UserSystemWechatConfigUnion::where('user_id',$user->id);

			//微信公众号
			if($openid_type == 10)
			{
				$userWechatOfficialObject = $query->where('type',30)->first();
			}
		}

        if($userWechatOfficialObject)
        {
            if(isset($userWechatOfficialObject?->openid) && $userWechatOfficialObject?->openid)
            {
                $openid = $userWechatOfficialObject->openid;
            }
        }

        return $openid;
   }

   
   /**
    * 获取用户角色ID数组
    *
    * @param object $user 用户对象，包含用户的ID
    * @return array 返回与用户关联的角色ID数组
    */
   public function getUserRoleIdArray($user)
   {
		$userRoleColection = UserRoleUnion::where('user_id',$user->id)->get();

	    $roleIdArray = [];

		foreach ($userRoleColection as $key => $item) 
		{
			$roleIdArray[] = $item->role_id;
		}

		return $roleIdArray;
   }
}
