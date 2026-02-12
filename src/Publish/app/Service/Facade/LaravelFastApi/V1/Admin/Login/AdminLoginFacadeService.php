<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 09:38:04
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-20 23:31:27
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Services\Facade\LaravelFastApi\V1\Admin\Login;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

use App\Exceptions\Admin\CommonException;

use App\Events\LaravelFastApi\V1\Admin\Login\AdminLoginEvent;
use App\Events\LaravelFastApi\V1\Admin\Login\AdminLogoutEvent;

use App\Jobs\LaravelFastApi\V1\Admin\Login\AdminLogoutJob;

use App\Facades\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacade;

use App\Http\Resources\LaravelFastApi\V1\Admin\Login\AdminInfoResource;


/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\Login\AdminLoginFacade
 */
class AdminLoginFacadeService
{
   public function test()
   {
       echo "AdminLoginFacadeService test";
   }

    /**
     * 后台管理员认证登录 账户|邮箱|手机 登录
     *
     * @param array $validated 验证通过传递过来的参数
     * @return array 返回值
     */
    public function authLogin(Array $validated = [])
    {
        $result = code(config('admin_code.AdminLoginError'));

        //p($validated);die;

        //设置默认守卫是admin
        Auth::setDefaultDriver('admin');

        if ( !isset($validated['username']) || !isset($validated['password']) )
		{
			throw new CommonException('ParamIsNullError');
		}
        
		$remember = true; //生成 remmber_token
		/**
		 * 注意数据库一旦生成了remember_token,再次登录是不变的,除主动清除或者更新,否则一直存在且不变
		 */
		$ip = Request::getClientIp(); //用户登录日志记录

		$verifyAccountNameResult = 0;
		$verifyEmailResult = 0;
		$verifyPhoneResult = 0;
		//验证账号
		$dataAccountName['account_name'] = $validated['username'];
		$dataAccountName['password'] = $validated['password'];
		$dataAccountName['switch'] = 1;

		$verifyAccountNameResult = Auth::attempt($dataAccountName, $remember);

		//p($verifyAccountNameResult);die;

		//账号验证失败
		if(!$verifyAccountNameResult)
		{
				//验证邮箱
			$dataEmail['email'] = $validated['username'];
			$dataEmail['password'] = $validated['password'];
			$dataEmail['switch'] = 1;

			$verifyEmailResult = Auth::attempt($dataEmail, $remember);

			//邮箱验证失败
			if(!$verifyEmailResult)
			{
				//验证手机号
				$dataPhone['phone'] = $validated['username'];
				$dataPhone['password'] = $validated['password'];
				$dataPhone['switch'] = 1;
				$verifyPhoneResult = Auth::attempt($dataPhone, $remember);
			}
		}

		if(!$verifyAccountNameResult && !$verifyEmailResult &&!$verifyPhoneResult)
		{
			throw new CommonException('AdminLoginError');
		}

		
		$admin = Auth::user();

		//p($admin);die;

		//无论如何 在redis缓存中执行一边重新再的登录
		AdminBackgroundLoginFacade::checkResetLogin($admin);

		$data['data'] = [];

		if (empty($admin) || !isset($admin->remember_token))
		{
			throw new CommonException('GetLoginAdminError');
		}

		$data['data']['token'] = $admin->remember_token;

		//adminLogin::dispatch($admin, $ip);
		AdminLoginEvent::dispatch($admin,$validated);

		//登录成功以后 12个小时以后数据库自动退出
		//AdminLogoutJob::dispatchIf($admin->remember_token, $admin)->delay(now()->addSeconds(3600 * 12));
		AdminLogoutJob::dispatchIf($admin->remember_token, $admin)->delay(now()->addSeconds(3600 * 12));

		$result = code(['code'=>0,'msg'=>'登录成功!'], $data);
		
		return $result;
    }

	/**
     * 用户退出登录
     *
     * @return void
    */
    public function logout($admin)
    {
        $result = [];

        $token = $admin->remember_token;

        $admin->remember_token = null;

        //UserUpdateToken
        $logoutResult = $admin->save();

        if (!$logoutResult)
        {
            throw new CommonException('AdminLogoutError');
        }

        AdminLogoutEvent::dispatch($admin,$token);


        $result = code(['code'=>0,'msg'=>'管理员退出成功!']);

        return $result;
    }

    /**
     * 获取管理员信息
     *
     * @param   $admin
     */
    public function getAdminInfo($admin)
    {

		$result = new AdminInfoResource($admin,['code'=>0,'msg'=>'获取管理员信息成功']);

		return $result;
    }
}
