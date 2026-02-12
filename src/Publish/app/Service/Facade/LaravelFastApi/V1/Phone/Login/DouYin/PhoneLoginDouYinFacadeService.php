<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-24 17:00:59
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-22 15:30:56
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login\DouYin;

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
 
use App\Events\LaravelFastApi\V1\Phone\User\Douyin\DouyinMiniGamesBindUserEvent;
use App\Events\LaravelFastApi\V1\Phone\User\UserLoginEvent;

use App\Models\LaravelFastApi\V1\System\Platform\SystemDouyinConfig;

use UserDouyinUnionid;
use UserSystemDouyinConfigUnion;

use App\Models\LaravelFastApi\V1\User\User;

use App\Facades\Common\V1\Login\CommonLoginFacade;
use App\Facades\Common\V1\User\User\CommonUserFacade;


/**
 * @see \App\Facades\Phone\Login\DouYin\PhoneLoginDouYinFacade
 */
class PhoneLoginDouYinFacadeService
{
   public function test()
   {
       echo "PhoneLoginDouYinFacadeService test";
   }

     /*
	* 通过抖音返回的openid等处理用户登录注册
	*
	* @param  [type] $douYinLoginResponseString
	*/
   public function LoginAndRegisterWithMiniGame($collection)
   {
	    //Log::debug(['$response'=>$response]);

		$appid = $collection->get('appid');

		$systemDouyinConfigId = $collection->get('systemDouyinConfigId');

		$douYinGamesLoginResponseString = $collection->get('response');

		$loginArray = json_decode($douYinGamesLoginResponseString,true);

		Log::debug(['$douyinGamesLoginObject'=>$loginArray]);
		
		['anonymous_openid'=>$anonymous_openid,'openid'=>$openid,'session_key'=>$session_key,'unionid'=>$unionid,'error'=>$error] = $loginArray;

		if($error > 0)
		{
			throw new CommonException('DouyinMiniGamesLoginError');
		}

		if($unionid)
		{
			//检测unionid是否存在
			$checkUnionIdResult = $this->checkHasUserByUnionid($unionid);

			if(!$checkUnionIdResult)
			{
				//注册用户
				$param['source'] = 30;
				$user_id = CommonUserFacade::registerUser($param);

				$this->bindUserDouyinUnionid($user_id,$unionid);
				$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
			}
			else
			{
				//要检测用户有没有这个openid
				$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemDouyinConfigId);
				
				if(!$checkOpenIdResult)
				{
					//如果没有就需要先绑定一下
					$userDouyinUnionidObject = UserDouyinUnionid::where('unionid',$unionid)->first();

					$user_id = $userDouyinUnionidObject->user_id;

					$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
				}
			}
		}
		else
		{
			$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemDouyinConfigId);

			//如果没有就注册
			if(!$checkOpenIdResult)
			{
				//注册用户
				$param['source'] = 30;
				$user_id = CommonUserFacade::registerUser($param);

				$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
			}
		}

		//执行登录
		$data = $this->loginUserByOpenid($openid,$systemDouyinConfigId);
		
		$result = code(['code'=>0,'msg'=>'server login success!'],$data);

		return $result;
   }

   /**
	* 抖音小程序登录
    */
   public function LoginAndRegisterWithMiniProgram($collection)
   {
		$appid = $collection->get('appid');

		$systemDouyinConfigId = $collection->get('systemDouyinConfigId');

		$douYinProgramsLoginResponseString = $collection->get('response'); 

		$loginArray = json_decode($douYinProgramsLoginResponseString,true);

		Log::debug(['$douyinProgramsLoginObject'=>$loginArray]);

		['err_no'=>$err_no,'err_tips'=>$err_tips,'log_id'=>$log_id,'data'=>$data] = $loginArray;

		if($err_no !== 0)
		{
			throw new CommonException('DouyinMiniProgramsLoginError');
		}

		['session_key'=>$session_key,'openid'=>$openid,'anonymous_openid'=>$anonymous_openid,'unionid'=>$unionid,'dopenid'=>$dopenid] = $data;

		if($unionid)
		{
			//检测unionid是否存在
			$checkUnionIdResult = $this->checkHasUserByUnionid($unionid);

			if(!$checkUnionIdResult)
			{
				//注册用户
				$param['source'] = 20;
				$user_id = CommonUserFacade::registerUser($param);

				$this->bindUserDouyinUnionid($user_id,$unionid);
				$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
			}
			else
			{
				//要检测用户有没有这个openid
				$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemDouyinConfigId);
				
				if(!$checkOpenIdResult)
				{
					//如果没有就需要先绑定一下
					$userDouyinUnionidObject = UserDouyinUnionid::where('unionid',$unionid)->first();

					$user_id = $userDouyinUnionidObject->user_id;

					$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
				}
			}
		}
		else
		{
			$checkOpenIdResult = $this->checkHasUserByOpenid($openid,$systemDouyinConfigId);

			//如果没有就注册
			if(!$checkOpenIdResult)
			{
				//注册用户
				$param['source'] = 20;
				$user_id = CommonUserFacade::registerUser($param);

				$this->bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key);
			}
		}


		//执行登录
		$resultData = $this->loginUserByOpenid($openid,$systemDouyinConfigId);
		
		$result = code(['code'=>0,'msg'=>'server login success!'],$resultData);

		return $result;

   }

   /**
	* 通过Unionid检测系统内是否已经注册用户
	*
	* @param  [type] $appid
	* @param  [type] $unionid
	* @param  [type] $openid
	*/
   protected function checkHasUserByUnionid($unionid)
   {
	    //检测结果
		$checkUnionIdResult = false;

		//检测unionid是否存在
		$userDouyinUnionidCount = UserDouyinUnionid::where('unionid',$unionid)->count();

		if($userDouyinUnionidCount)
		{
			$checkUnionIdResult = true;
		}

		return $checkUnionIdResult;
   }

   /**
	* 检测用户是否有openid
    */
   protected function checkHasUserByOpenid($openid,$systemDouyinConfigId)
   {
		//检测结果
	   $checkOpenIdResult = false;

	   $userSystemDouyinConfigUnionCount = UserSystemDouyinConfigUnion::where('openid',$openid)->where('system_douyin_config_id',$systemDouyinConfigId)->count();

	   if($userSystemDouyinConfigUnionCount)
	   {
			$checkOpenIdResult = true;
	   }

	   return $checkOpenIdResult;
   }

    /**
	* 绑定用户和微信Unionid
	*/
   protected function bindUserDouyinUnionid($user_id,$unionid)
   {
		$userDouyinUnionidObject = new UserDouyinUnionid;

		$userDouyinUnionidObject->user_id = $user_id;
		$userDouyinUnionidObject->unionid = $unionid;
		$userDouyinUnionidObject->created_at = time();
		$userDouyinUnionidObject->created_time = time();

		$userDouyinUnionidResult = $userDouyinUnionidObject->save();

		if(!$userDouyinUnionidResult)
		{
			throw new CommonException('BindUserDouyinUnionidError');
		}
   }
   
    /**
	* 绑定用户和微信Openid
    */
   protected function bindUserDouyinOpenid($user_id,$openid,$systemDouyinConfigId,$session_key)
   {
		$userSystemDouyinConfigUnionObject = new UserSystemDouyinConfigUnion;

		$userSystemDouyinConfigUnionObject->user_id = $user_id;
		$userSystemDouyinConfigUnionObject->openid = $openid;
		$userSystemDouyinConfigUnionObject->session_key = $session_key;
		$userSystemDouyinConfigUnionObject->system_douyin_config_id = $systemDouyinConfigId;
		$userSystemDouyinConfigUnionObject->verified_at = date('Y-m-d H:i:s',time());
		$userSystemDouyinConfigUnionObject->verified_time = time();
		$userSystemDouyinConfigUnionObject->created_at = time();
		$userSystemDouyinConfigUnionObject->created_time = time();

		$userSystemDouyinConfigUnionResult = $userSystemDouyinConfigUnionObject->save();

		if(!$userSystemDouyinConfigUnionResult)
		{
			throw new CommonException('BindUserDouyinOpendidError');
		}
   }

   /**
	* 通过openid登录用户
	*
	* @param  [type] $openid
	* @param  [type] $systemDouyinConfigId
	*/
   protected function loginUserByOpenid($openid,$systemDouyinConfigId)
   {
	    $userSystemDouyinConfigUnionObject =  UserSystemDouyinConfigUnion::where('openid',$openid)->where('system_douyin_config_id',$systemDouyinConfigId)->first();

	    $userId = $userSystemDouyinConfigUnionObject->user_id;

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
			$data['data']['user_id'] = $user->id;
			$data['data']['open_id'] = $openid;
		}

		return $data;
   }
}
