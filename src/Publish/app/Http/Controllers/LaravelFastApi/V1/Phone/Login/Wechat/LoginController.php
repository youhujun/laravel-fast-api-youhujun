<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2026-01-07 13:47:35
 * @FilePath: /app/Http/Controllers/LaravelFastApi/V1/Phone/Login/Wechat/LoginController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat;

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

use App\Exceptions\Common\RuleException;

use App\Facades\Pub\V1\Wechat\WechatLoginFacade;
use App\Facades\Pub\V1\Wechat\WechatOfficialFacade;

use App\Facades\LaravelFastApi\V1\Phone\Login\Wechat\PhoneLoginWechatFacade;

class LoginController extends Controller
{


    /**
     * 获取微信授权码的 URL
     *
     * 此方法接收请求参数并验证其有效性，然后调用微信官方接口获取授权码的 URL。
     *
     * @param Request $request 请求对象，包含以下参数：
     * - scope_type: 必填，数字类型，表示授权范围类型。
     * - state: 必填，数字类型，表示状态参数。
     * - auth_redirect_url: 必填，字符串类型，表示授权后重定向的 URL。
     *
     * @return mixed 返回获取的授权码 URL 或错误代码。
     */
    public function toGetCodeUrl(Request $request)
    {
        $result =  code(config('common_code.WechatOfficialGetCodeError'));

		$validator = Validator::make(
            $request->all(),
            [
				'scope_type'=>['bail',new Required,new Numeric],
				'state'=>['bail',new Required,new Numeric],
				'auth_redirect_url'=>['bail',new Required,new CheckString],
            ],
            [ ]
        );

		$validated = $validator->validated();

		if(!isset($validated['scope_type'])){
			throw new RuleException('RuleRequiredError','scope_type');
		}

		if(!isset($validated['state'])){
			throw new RuleException('RuleRequiredError','state');
		}

		if(!isset($validated['auth_redirect_url'])){
			throw new RuleException('RuleRequiredError','auth_redirect_url');
		}

		
		$result = WechatOfficialFacade::toGetCodeUrl($validated);
		
        return $result;
    }


    /**
     * 通过微信授权码进行登录认证
     *
     * 此方法接收请求中的授权码和状态，验证输入后调用微信官方接口进行登录。
     * 根据返回的授权结果，获取用户信息并处理登录或注册逻辑。
     *
     * @param Request $request 请求对象，包含授权码和状态
     * @return mixed 返回登录结果或错误信息
     *
     * @throws \Illuminate\Validation\ValidationException 如果验证失败
     */
    public function authToLoginByCode(Request $request)
    {
        $result =  code(config('common_code.WechatOfficialAuthError'));

        $validated = $request->validate(
        [
            'code'=>['bail',new Required,new CheckString],
            'state'=>['bail','nullable',new CheckString],
        ],
        []);

		if(!isset($validated['code'])){
			throw new RuleException('RuleRequiredError','code');
		}

        //p($validated);die;
        $collection = WechatOfficialFacade::authToLoginByCode($validated);

		plog(['WechattWebAuth'=>$collection],'Wechat','WechatWebAuth');

		$state = $validated['state'];

		//登录并注册 
		if($state == 20)
		{
			$result = PhoneLoginWechatFacade::loginAndRegisterWithOfficial($collection);
		}

        return $result;
    }

	/**
	 * 获取微信登录的授权码URL
	 *
	 * 此方法用于生成微信登录的授权码URL。首先验证请求参数，确保所有必需的字段都已提供且格式正确。
	 * 如果用户具备相应权限，则调用微信官方接口获取授权码URL。
	 *
	 * @param Request $request 请求对象，包含用户输入的数据
	 * @return mixed 返回生成的授权码URL或错误信息
	 */
	public function toGetCodeUrlWithLogin(Request $request)
    {

        $result = code(\config('phone_code.PhoneAuthError'));

		$user = Auth::guard('phone_token')->user();

		if(Gate::forUser($user)->allows('phone-user-role'))
		{
			$validator = Validator::make(
				$request->all(),
				[
					'scope_type'=>['bail',new Required,new Numeric],
					'state'=>['bail',new Required,new Numeric],
					'auth_redirect_url'=>['bail',new Required,new CheckString],
				],
				[ ]
			);

			$validated = $validator->validated();

			if (!isset($validated['scope_type'])) {
				throw new RuleException('RuleRequiredError', 'scope_type');
			}

			if (!isset($validated['state'])) {
				throw new RuleException('RuleRequiredError', 'state');
			}

			if (!isset($validated['auth_redirect_url'])) {
				throw new RuleException('RuleRequiredError', 'auth_redirect_url');
			}

			if(count($validated))
			{
				$result = WechatOfficialFacade::toGetCodeUrl(f($validated));
			}
		}

        return $result;
    }

	/**
	 * 通过验证码进行登录授权
	 *
	 * 此方法处理用户通过验证码进行登录的请求。首先验证请求中的验证码和状态参数，
	 * 然后调用微信官方接口进行登录授权。如果用户已登录且状态为绑定，则将当前用户与
	 * 微信账号进行绑定。最终返回登录结果。
	 *
	 * @param Request $request 请求对象，包含验证码和状态参数
	 * @return mixed 登录结果，可能包含用户信息或错误信息
	 */
	public function authToLoginByCodeWithLogin(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

		$user = Auth::guard('phone_token')->user();

		if(Gate::forUser($user)->allows('phone-user-role'))
		{
			$validated = $request->validate(
			[
				'code'=>['bail',new Required,new CheckString],
				'state'=>['bail','nullable',new CheckString],
			],
			[			]);

			if (!isset($validated['code'])) {
				throw new RuleException('RuleRequiredError', 'code');
			}

			$collection = WechatOfficialFacade::authToLoginByCode(f($validated));

			plog(['$collection'=>$collection,'msg'=>'微信登录授权结果']);

			$state = $validated['state'];

			//授权绑定 已经登录,只是绑定
			if($state == 10)
			{
				$result = PhoneLoginWechatFacade::bindUserByOfficial($collection,$user);
			}
		}

		return $result;

    }
	
	/**
     * 微信小程序登录
     */
    public function getOpenIdByCodeWithMiniProgram(Request $request)
    {
       $result = code(\config('phone_code.WechatMiniProgramLoginError'));

        $validator = Validator::make(
            $request->all(),
            [
				'appid'=>['bail',new Required,new CheckString],
                'code'=>['bail',new Required,new CheckString],
            ],
            [ ]
        );

		$validated = $validator->validated();

		if (!isset($validated['appid'])) {
			throw new RuleException('RuleRequiredError', 'appid');
		}

		if (!isset($validated['code'])) {
			throw new RuleException('RuleRequiredError', 'code');
		}

		//WechatLoginFacade::test();die;

		$collection = WechatLoginFacade::getOpenIdByCodeWithMiniProgram($validated);

		$result = PhoneLoginWechatFacade::LoginAndRegisterWithMiniProgram($collection);

		return $result;

    }

	 /**
     * 微信小游戏登录
     */
    public function getOpenIdByCodeWithMiniGame(Request $request)
    {
       $result = code(\config('phone_code.WechatMiniGameLoginError'));

        $validator = Validator::make(
            $request->all(),
            [
				'appid'=>['bail',new Required,new CheckString],
                'code'=>['bail',new Required,new CheckString],
            ],
            [ ]
        );

		$validated = $validator->validated();

		if (!isset($validated['appid'])) {
			throw new RuleException('RuleRequiredError', 'appid');
		}

		if (!isset($validated['code'])) {
			throw new RuleException('RuleRequiredError', 'code');
		}

		//WechatLoginFacade::test();die;

		$collection = WechatLoginFacade::getOpenIdByCodeWithMiniProgram($validated);

		$result = PhoneLoginWechatFacade::LoginAndRegisterWithMiniGame($collection);

		return $result;

    }

    
}
