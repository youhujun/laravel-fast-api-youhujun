<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 09:58:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 11:29:22
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Phone\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

use App\Exceptions\Phone\CommonException;
use App\Events\Phone\CommonEvent;


use App\Models\LaravelFastApi\V1\User\User;

use App\Facades\Pub\V1\Sms\SmsFacade;
use App\Facades\Common\V1\User\User\CommonUserFacade;

/**
 * @see \App\Facades\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacade
 */
class PhoneRegisterFacadeService
{
   public function test()
   {
       echo "PhoneRegisterFacadeService test";
   }

   /**
    * 发送注册验证码
    */
   public function sendUserRegisterCode($validated)
   {
        $result = code(config('phone_code.SendUserRegisterCodeError'));

		//默认发送成功
		$sendResult = 1;
		//先查看是否已经发送过验证码
		$registerCode = Redis::get("sms-register:{$validated['phone']}");

		//如果没有再发送
		if(!$registerCode)
		{
			$sendResult = SmsFacade::sendUserRegisterCode($validated['phone']);
		}
		

		if($sendResult)
		{
			$result =  code(['code'=>0,'msg'=>'发送注册验证码成功']);
		}

        return $result;
   }

   /**
    * 添加用户
    */
   public function userRegister($validated)
   {
        $result = code(config('phone_code.AddUserError'));

		//如果是开发模式就不校验短信验证码
		if(!config('youhujun.develop_mode')){
			 //1先比对用户的验证码
			$redisCode = SmsFacade::getUserRegisterCode($validated['phone']);

			if(!$redisCode)
			{
				throw new CommonException('UserRegisterCodeTimeError');
			}

			if($redisCode != $validated['registerCode'])
			{
				throw new CommonException('UserRegisterCodeError');
			}

			if(!isset($validated['phone']) || !isset($validated['password']))
			{
				throw new CommonException('AddUserError');
			}

		}

		DB::beginTransaction();

		$user = new User;

		$user->phone = $validated['phone'];

		$user->password = Hash::make(trim($validated['password']));

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

		CommonUserFacade::registerUser($validated,$user);

		CommonEvent::dispatch($user,$validated,'AddUser',1);

		//提交
		DB::commit();

        $result = code(['code'=> 0,'msg'=>'注册用户成功!']);

        return $result;
   }
}
