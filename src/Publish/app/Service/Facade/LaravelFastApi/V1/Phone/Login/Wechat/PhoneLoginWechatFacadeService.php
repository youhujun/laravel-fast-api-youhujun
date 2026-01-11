<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-28 15:08:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-20 11:38:42
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\Login\Wechat;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

use App\Exceptions\Phone\CommonException;

use App\Events\Phone\CommonEvent;
 
use App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent;

use App\Models\LaravelFastApi\V1\System\Platform\SystemWechatConfig;
use App\Models\LaravelFastApi\V1\User\Platform\UserWechatUnionid;
use App\Models\LaravelFastApi\V1\User\Union\UserSystemWechatConfigUnion;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;

use App\Facade\Common\V1\Login\CommonLoginFacade;
use App\Facade\Common\V1\User\User\CommonUserFacade;

/**
 * @see \App\Facade\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacade
 */
class PhoneLoginWechatFacadeService
{
   public function test()
   {
       echo "PhoneLoginWechatFacadeService test";
   }

    /**
    * 通过微信官方接口登录或注册用户。
    *
    * 此方法首先检查用户是否存在，如果用户不存在，则根据提供的 unionid 或 openid 注册新用户。
    * 如果用户已存在，则将微信的 openid 绑定到用户账户上。最后，执行用户登录操作。
    *
    * @param \Illuminate\Support\Collection $collection 包含认证结果和用户信息的集合。
    * @return array 返回登录结果，包括状态码和消息。
    */
   	public function loginAndRegisterWithOfficial($collection)
	{
		plog(['loginAndRegisterWithOfficial'=>$collection],'LoginAndRegister','LoginAndRegister');

		$authResultObject = $collection->get('authResultArray');
		$wechatOfficial = $collection->get('wechatOfficialObject');

		//plog(['authResultArray'=>$authResultObject],'LoginAndRegister','authResultArray');
		//plog(['wechatOfficialObject'=>$wechatOfficial],'LoginAndRegister','wechatOfficialObject');

		$openid = $authResultObject['openid'];
		$systemWechatConfigId = $wechatOfficial->id; 

		$propertyCollection = collect([
			'access_token'=>$authResultObject['access_token'],
			'expires_in'=>$authResultObject['expires_in'],
			'refresh_token'=>$authResultObject['refresh_token'],
			'scope'=>$authResultObject['scope'],
			'type'=>30
		]);

		$userInfoColection = null;

		if(isset($authResultObject['userinfo']) && isset($authResultObject['userinfo']['openid']))
		{
			$userInfoColection = collect([
				'nickname'=>$authResultObject['userinfo']['nickname'],
				'sex'=>$authResultObject['userinfo']['sex'],
				'headimgurl'=>$authResultObject['userinfo']['headimgurl']
			]);
		}

		// 如果有unionid
		// 4. 修改：判断数组是否有 unionid 键（而非对象属性）
		if(isset($authResultObject['unionid']))
		{
			$unionid = $authResultObject['unionid'];

			// 通过Unionid检测用户是否存在
			$checkUnionIdResult = $this->checkHasUserByUnionid($unionid);

			// 不存在就注册用户
			if(!$checkUnionIdResult)
			{
				// 注册用户
				$param['source'] = 40;
				$user = CommonUserFacade::registerUser($param);

				$user_id = $user->id;

				$this->bindUserWechatUnionid($user_id,$unionid);

				$this->bindUserWechatOpenid($user_id,$openid,$systemWechatConfigId,$propertyCollection);
			}
			else
			{
				// 检测openid
				$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemWechatConfigId);

				// 如果没有就需要绑定
				if(!$checkOpenIdResult)
				{
					$userWechatUnionidObject = UserWechatUnionid::where('unionid',$unionid)->first();

					$user_id = $userWechatUnionidObject->user_id;

					$this->bindUserWechatOpenid($user_id,$openid,$systemWechatConfigId,$propertyCollection);
				}
			}
		}
		else
		{
			// 没有直接检测 openid 
			$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemWechatConfigId);

			// 如果没有就注册
			if(!$checkOpenIdResult)
			{
				// 注册用户
				$param['source'] = 50;
				$user = CommonUserFacade::registerUser($param);

				$user_id = $user->id;

				$this->bindUserWechatOpenid($user_id,$openid,$systemWechatConfigId,$propertyCollection);
			}
			else
			{
				$user_id = UserSystemWechatConfigUnion::where('openid',$openid)->first()->user_id;

				$user = User::find($user_id);
			}
		}

		$this->bindWeChatUserInfo($user,$userInfoColection);

		// 执行登录
		$data = $this->loginUserByOpenid($openid,$systemWechatConfigId);
		
		$result = code(['code'=>0,'msg'=>'server login success!'],$data);

		return $result;
	}

   /**
	* 通过uninodid检测用户是否存在 
	*/
   public function checkHasUserByUnionid($unionid)
   {
		//检测结果
		$checkUnionIdResult = false;

		//检测unionid是否存在
		$userWechatUnionidCount = UserWechatUnionid::where('unionid',$unionid)->count();

		if($userWechatUnionidCount)
		{
			$checkUnionIdResult = true;
		}

		return $checkUnionIdResult;
   }

   /**
	* 检测用户是否有openid
	*
	* @param  [type] $openid
	*/
   public function checkHasUserByOpenid($openid,$systemWechatConfigId)
   {
	   //检测结果
	   $checkOpenIdResult = false;

	   //检测unionid是否存在
	   $userWechatOpenidCount = UserSystemWechatConfigUnion::where('openid',$openid)->where('system_wechat_config_id',$systemWechatConfigId)->count();

	   if($userWechatOpenidCount)
	   {
			$checkOpenIdResult = true;
	   }

	   return $checkOpenIdResult;
   }


   /**
    * 绑定用户的微信 UnionID
    *
    * @param int $user_id 用户的 ID
    * @param string $unionid 用户的微信 UnionID
    * @throws CommonException 如果绑定失败，则抛出异常
    */
   protected function bindUserWechatUnionid($user_id,$unionid)
   {
		$userWechatUnionidObject = new UserWechatUnionid;

		$userWechatUnionidObject->user_id = $user_id;
		$userWechatUnionidObject->unionid = $unionid;
		$userWechatUnionidObject->created_at = time();
		$userWechatUnionidObject->created_time = time();

		$userWechatUnionidResult = $userWechatUnionidObject->save();

		if(!$userWechatUnionidResult)
		{
			throw new CommonException('BindUserWechatUnionidError');
		}
   }
   

   /**
    * 绑定用户的微信 OpenID 到系统配置中
    *
    * @param int $user_id 用户 ID
    * @param string $openid 微信 OpenID
    * @param int $systemWechatConfigId 系统微信配置 ID
    * @param PropertyCollection $propertyCollection 属性集合，包含微信用户的相关信息
    * 
    * @throws CommonException 如果绑定失败，抛出异常
    */
   protected function bindUserWechatOpenid($user_id,$openid,$systemWechatConfigId,$propertyCollection)
   {
		$userSystemWechatConfigUnionObject = new UserSystemWechatConfigUnion;

		$userSystemWechatConfigUnionObject->user_id = $user_id;
		$userSystemWechatConfigUnionObject->openid = $openid;
		$userSystemWechatConfigUnionObject->system_wechat_config_id = $systemWechatConfigId;
		$userSystemWechatConfigUnionObject->verified_at = date('Y-m-d H:i:s',time());
		$userSystemWechatConfigUnionObject->verified_time = time();
		$userSystemWechatConfigUnionObject->created_at = time();
		$userSystemWechatConfigUnionObject->created_time = time();

		$userSystemWechatConfigUnionObject->type = $propertyCollection->get('type')?$propertyCollection->get('type'):0;
		$userSystemWechatConfigUnionObject->session_key = $propertyCollection->get('session_key')?$propertyCollection->get('session_key'):'';
		$userSystemWechatConfigUnionObject->access_token = $propertyCollection->get('access_token')?$propertyCollection->get('access_token'):'';
		$userSystemWechatConfigUnionObject->expires_in = $propertyCollection->get('expires_in')?$propertyCollection->get('expires_in'):0;
		$userSystemWechatConfigUnionObject->refresh_token = $propertyCollection->get('refresh_token')?$propertyCollection->get('refresh_token'):'';
		$userSystemWechatConfigUnionObject->refresh_token = $propertyCollection->get('refresh_token')?$propertyCollection->get('refresh_token'):'';

		$userSystemWechatConfigUnionObject->scope = $propertyCollection->get('scope')?$propertyCollection->get('scope'):'';
		$userSystemWechatConfigUnionObject->is_snapshotuser = $propertyCollection->get('is_snapshotuser')?$propertyCollection->get('is_snapshotuser'):0;

		$userSystemWechatConfigUnionResult = $userSystemWechatConfigUnionObject->save();

		if(!$userSystemWechatConfigUnionResult)
		{
			throw new CommonException('BindUserWechatOpendidError');
		}
   }

   
   /**
    * 绑定微信用户信息到系统用户。
    *
    * @param User $user 系统用户对象。
    * @param Collection $userInfoColection 微信用户信息集合。
    * 
    * @throws CommonException 如果绑定用户信息或头像失败，将抛出异常。
    */
   protected function bindWeChatUserInfo($user,$userInfoColection)
   {
		
		if($userInfoColection && $userInfoColection->count())
		{
			//先处理用户详情
			
			$userInfoObject =  UserInfo::where('user_id',$user->id)->first();

			if(!$userInfoObject)
			{
				$userInfoObject = new UserInfo;

				$userInfoObject->user_id = $user->id;
				$userInfoObject->nick_name = $userInfoColection->get('nickname')?$userInfoColection->get('nickname'):'';
				$sexArray = ['0'=>0,'1'=>10,'2'=>20];
				$userInfoObject->sex = $sexArray[$userInfoColection->get('sex')];

				$userInfoObject->created_at = time();
				$userInfoObject->created_time = time();

				$userInfoResult = $userInfoObject->save();

				if(!$userInfoResult)
				{
					throw new CommonException('BindUserWechatUserInfoError');
				}
			}
			else
			{
				$userInfoObject->nick_name = $userInfoColection->get('nickname')?$userInfoColection->get('nickname'):'';
				$sexArray = ['0'=>0,'1'=>10,'2'=>20];
				$userInfoObject->sex = $sexArray[$userInfoColection->get('sex')];

				$userInfoObject->updated_at = time();
				$userInfoObject->updated_time = time();

				$userInfoResult = $userInfoObject->save();

				if(!$userInfoResult)
				{
					throw new CommonException('UpateUserWechatUserInfoError');
				}
			}

			//再处理用户头像

			$userAlbumObject = Album::where('user_id',$user->id)->where('album_type',20)->where('is_default',1)->first();

			$albumPictureObject = new AlbumPicture;

			$albumPictureObject->user_id = $user->id;
			$albumPictureObject->album_id = $userAlbumObject->id?$userAlbumObject->id:0;
			$albumPictureObject->picture_type = 30;
			$albumPictureObject->picture_tag = 'avatar';
			$albumPictureObject->picture_spec = '300x200';
			$albumPictureObject->picture_name = $userInfoObject->nick_name;
			$albumPictureObject->picture_url = $userInfoColection->get('headimgurl')?$userInfoColection->get('headimgurl'):'';;
			
			$albumPictureObject->created_at = time();
			$albumPictureObject->created_time = time();
			$albumPictureResult = $albumPictureObject->save();

			if(!$albumPictureResult)
			{
				throw new CommonException('BindUserWechatAlbumPictureError');
			}

			$userAvatarObject =  new UserAvatar;

			$userAvatarObject->user_id = $user->id;
			$userAvatarObject->album_picture_id = $albumPictureObject->id;
			$userAvatarObject->created_at = time();
			$userAvatarObject->created_time = time();

			$userAvatarResult = $userAvatarObject->save();

			if(!$userAvatarResult)
			{
				throw new CommonException('BindUserWechatUserAvatarError');
			}

		}
   }

    /**
	* 通过openid登录用户
	*
	* @param  [type] $openid
	* @param  [type] $systemWechatConfigId
	*/
   protected function loginUserByOpenid($openid,$systemWechatConfigId)
   {
	    $userSystemWechatConfigUnionObject =  UserSystemWechatConfigUnion::where('openid',$openid)->where('system_wechat_config_id',$systemWechatConfigId)->first();

	    $userId = $userSystemWechatConfigUnionObject->user_id;

		$data = null;

	    $user = User::find($userId);

		if($user)
		{
			Auth::setDefaultDriver('phone');

			$remember = true;

			Auth::login($user,$remember);

			//验证现在这个用户是否是登录状态
			CommonLoginFacade::checkResetLogin($user);

			UserLoginEvent::dispatch($user);

			$data['data']['token'] = $user->remember_token;
			$data['data']['userid'] = $user->id;
			$data['data']['openid'] = $openid;
			$data['data']['phone'] = $user->phone;
			
		}

		return $data;
   }
}
