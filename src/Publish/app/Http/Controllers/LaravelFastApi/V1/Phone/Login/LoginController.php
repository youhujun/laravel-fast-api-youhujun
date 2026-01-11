<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 10:32:47
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 11:16:12
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\Login\LoginController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;

use App\Rules\LaravelFastApi\V1\Phone\LoginPhone;
use App\Rules\LaravelFastApi\V1\Phone\Phone;

use App\Exceptions\Common\RuleException;

use App\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacade;

class LoginController extends Controller
{
	  /**
     * 通过手机号 密码登录
     *
     * @param LoginRequest $request
     * @return void
     */
    public function loginByPhonePassword(Request $request)
    {
        $result = code(\config('phone_code.LoginByUserError'));

         $validator = Validator::make(
            $request->all(),
            [
                'phone'=>['bail',new Required,new CheckString,new LoginPhone],
                'password'=>['bail',new Required,new CheckString,new CheckBetween(6,12)], 
            ],
            [ ]
        );

        $validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

		if(!isset($validated['password'])){
			throw new RuleException('RuleRequiredError','password');
		}
		//p($validated);die
        $result = PhoneLoginFacade::loginByPhonePassword(f($validated));

        return $result;
    }

     /**
     * 发送手机验证码
     *
     * @param Request $request
     * @return void
     */
    public function sendVerifyCode(Request $request)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

        $validator = Validator::make(
            $request->all(),
            [
               'phone'=>['bail',new Required,new CheckString,new LoginPhone],
            ],
            [ ]
        );

        $validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

        $result = PhoneLoginFacade::sendVerifyCode($validated);

        return $result;
    }

     /**
     * 通过手机号验证码登录
     *
     * @param Request $request
     * @return void
     */
    public function loginByPhoneCode(Request $request)
    {
         $result = code(\config('phone_code.LoginByPhoneError'));
         $validator = Validator::make(
            $request->all(),
            [
                'phone'=>['bail',new Required,new CheckString,new LoginPhone],
                'code'=>['bail',new Required,new CheckString,'regex:/^\d{4}$/'],
            ],
            [ ]
        );

        $validated = $validator->validated();

        //p($validated);die;
		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

		if(!isset($validated['code'])){
			throw new RuleException('RuleRequiredError','code');
		}

        $result = PhoneLoginFacade::loginByPhoneCode(f($validated));

        return $result;

    }

	 /**
     * 发送手机验证码 忘记密码
     *
     * @param Request $request
     * @return void
     */
    public function sendPasswordCode(Request $request)
    {
		
        $result = code(config('phone_code.SendPhoneCodeError'));

        $validator = Validator::make(
            $request->all(),
            [
               'phone'=>['bail',new Required,new CheckString,new LoginPhone],
            ],
            [ ]
        );

        $validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

        $result = PhoneLoginFacade::sendVerifyCode($validated);

        return $result;
    }

	/**
	 * 重置手机密码
	 *
	 * @param  Request $request
	 */
	public function restPasswordByPhone(Request $request)
	{
		 $result = code(\config('phone_code.RestPasswordByPhoneError'));

		 $validator = Validator::make(
            $request->all(),
            [
                'phone'=>['bail',new Required,new CheckString,new LoginPhone],
                'code'=>['bail',new Required,new CheckString,'regex:/^\d{4}$/'],
				'password'=>['bail',new Required,new CheckString,new CheckBetween(6,12)],
            ],
            [ ]
        );

		$validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

		if(!isset($validated['code'])){
			throw new RuleException('RuleRequiredError','code');
		}

		if(!isset($validated['password'])){
			throw new RuleException('RuleRequiredError','password');
		}

		$result = PhoneLoginFacade::restPasswordByPhone($validated);

        return $result;


	}

    /**
     * app 一键登录注册
     *
     * @param Request $request
     * @return void
     */
    // public function univerifyLogin(Request $request)
    // {
    //     $validated = $request->validate(
    //     [
    //         'provider'=>['bail',new Required,new CheckString],
    //         'openid'=>['bail',new Required,new CheckString],
    //         'access_token'=>['bail',new Required,new CheckString],
    //     ],
    //     [
    //         'provider.required' => '必须有provider',
    //         'openid.required' => '必须有openid',
    //         'access_token.required' => '必须有access_token',
    //     ]);

    //     $validated['ip'] = $request->getClientIp();

    //     //Log::debug(['$validated'=> $validated]);

    //     //p($validated);die;

    //     $result = PhoneLoginFacade::univerifyLogin($validated);

    //     return $result;

    // }

    /**
     * 通过用户id登录,开发测试用
     *
     * @param  Request $request
     */
    public function loginByUserId(Request $request)
    {
        $validated = $request->validate(
        [
            'user_id'=>['bail',new Required,new Numeric],
        ],[]);

        if (!isset($validated['user_id'])) {
            throw new RuleException('RuleRequiredError', 'user_id');
        }

        //p($validated);die;

        $result = PhoneLoginFacade::loginByUserId($validated);

        return $result;

    }




	 /**
     * 发送手机验证码
     *
     * @param Request $request
     * @return void
     */
    public function sendBindCode(Request $request)
    {
        $result = code(config('phone_code.SendPhoneCodeError'));

		$user = Auth::guard('phone_token')->user();

		if(Gate::forUser($user)->allows('phone-user-role'))
        {
			$validator = Validator::make(
				$request->all(),
				[
				'phone'=>['bail',new Required,new CheckString,new Phone],
				],
				[ ]
        	);

			$validated = $validator->validated();

			if(!isset($validated['phone'])){
				throw new RuleException('RuleRequiredError','phone');
			}

			$result = PhoneLoginFacade::sendVerifyCode($validated);
		}
        
        return $result;
    }

	/**
	 * 微信登录绑定手机号
	 *
	 * @param  Request $request
	 */
	public function bindPhone(Request $request)
	{
		$user = Auth::guard('phone_token')->user();

		$result = code(\config('phone_code.PhoneAuthError'));

		if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validated = $request->validate(
            [
				'phone'=>['bail',new Required,new CheckString,new CheckUnique('users','phone')],
                'code'=>['bail',new Required,new CheckString,'regex:/^\d{4}$/'],
                'password'=>['bail',new Required,new CheckString,new CheckBetween(6,12)],
            ],
            []);
			
			if(!isset($validated['phone'])){
				throw new RuleException('RuleRequiredError','phone');
			}

			if(!isset($validated['code'])){
				throw new RuleException('RuleRequiredError','code');
			}

			if(!isset($validated['password'])){
				throw new RuleException('RuleRequiredError','password');
			}
            //p($validated);die;

            $result = PhoneLoginFacade::bindPhone(f($validated),$user);
        }

		return $result;
	}


    /**
     * 检测是否定登录
     *
     * @return void
     */
    public function checkIsLogin(Request $request)
    {
        $result = code(['code'=> 0,'msg'=>'用户已经成功登录!']);

        return $result;
    }

	/**
     * 获取用户信息
     *
     * @param Request $request
     * @return void
     */
    public function getUserInfo(Request $request)
    {
		$result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
			$validator = Validator::make(
				$request->all(),
				[
					'openid_type'=>['bail',new Required,new Numeric],
				],
				[ ]
        	);

			$validated = $validator->validated();

			if(count($validated))
			{
				$result = PhoneLoginFacade::getUserInfo(f($validated),$user);
			}
        }

        return $result;
    }

    /**
     * 用户退出
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

		//p($user);die;

		$result = code(\config('phone_code.PhoneAuthError'));

		if(Gate::forUser($user)->allows('phone-user-role'))
        {
			$result = PhoneLoginFacade::logout($user);
		}
		
        return $result;
    }





}
