<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-05 10:35:13
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 09:21:53
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Services\Facade\Traits\QueryService;

use App\Exceptions\Phone\CommonException;

use App\Events\Phone\CommonEvent;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;

use App\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserInfoResource;

/**
 * @see \App\Facades\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacade
 */
class PhoneUserInfoFacadeService
{
   public function test()
   {
       echo "PhoneUserInfoFacadeService test";
   }

   /**
	* 获取用户信息
	*
	* @param  [type] $validated
	* @param  [type] $user
	*/
   public function getUserInfo($validated,$user)
   {
		$result = code(config('phone_code.GetUserInfoError'));

		$result = ['code'=>0,'msg'=>'获取用户信息成功','data'=>new UserInfoResource($user)];

		return $result;
   }

	/**
	 * 更新用户昵称
	 *
	 */
	public function updateUserNickName($validated,$user)
	{
		$result = code(config('phone_code.UpdateUserNickNameError'));

		$userInfoObject = UserInfo::where('user_id',$user->id)->first();

		if(!$userInfoObject)
		{
			throw new CommonException('ThisDataNotExistsError');
		}

		$where= [];

		$where[] = ['id','=',$userInfoObject->id];
		$where[] = ['revision','=',$userInfoObject->revision];

		$updateData = [];

		$updateData['nick_name'] = $validated['nick_name'];
		$updateData['revision'] = $userInfoObject->revision + 1;

		$updateResult = UserInfo::where($where)->update($updateData);

		if(!$updateResult)
		{
			throw new CommonException('UpdateUserNickNameError');
		}

		CommonEvent::dispatch($user,$validated,'UpdateUserNickName');

		$result = code(['code'=>0,'msg'=>'更新用户昵称成功']);

        return $result;

	}

	/**
	 * 更新用户手机号
	 *
	 */
	public function updateUserPhone($validated,$user)
	{
		$result = code(config('phone_code.UpdateUserPhoneError'));

		$where= [];
		$where[] = ['id','=',$user->id];
		$where[] = ['revision','=',$user->revision];

		$updateData = [];
		$updateData['phone'] = $validated['phone'];
		$updateData['revision'] = $user->revision + 1;

		$updateResult = User::where($where)->update($updateData);

		if(!$updateResult)
		{
			throw new CommonException('UpdateUserPhoneError');
		}

		CommonEvent::dispatch($user,$validated,'UpdateUserPhone');

		$result = code(['code'=>0,'msg'=>'更新用户手机号成功']);

        return $result;
	}

}
