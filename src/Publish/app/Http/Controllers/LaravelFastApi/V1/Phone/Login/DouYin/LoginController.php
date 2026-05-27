<?php
/*
 * @Descripttion: 抖音开发业务用
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-24 14:44:48
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-27 02:03:42
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin\LoginController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

//DTO
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\DouYin\GetOpenIdByCodeWithMiniGameDTO;
use App\DTOs\LaravelFastApi\V1\Phone\User\Login\DouYin\GetOpenIdByCodeWithMiniProgramDTO;

use App\Facades\Pub\V1\DouYin\DouYinFacade;

use App\Facades\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacade;

/**
 * @see \App\Services\Facade\Pub\V1\DouYin\DouYinFacadeService
 */
class LoginController extends Controller
{

	/**
	 * 微信小游戏登录获取code
	 *
	 * @param  Request $request
	 */
    public function getOpenIdByCodeWithMiniGame(Request $request)
	{		
		$result = code(\config('phone_code.DouyinMiniGameLoginError'));

        $requestDTO = (new GetOpenIdByCodeWithMiniGameDTO())->validate($request->all());

		$collection = DouYinFacade::getOpenIdByCodeWithMiniGame($requestDTO);

		//$response = '{"anonymous_openid":"","error":0,"openid":"_000yuNDRxaa6Ff74MuT9oej9SCp5lZwbF2n","session_key":"RCREAC28cjgLb7pvf0FYAw==","unionid":"c2e48549-c69e-5879-9749-9a260d70d43f"}';

		$result = PhoneLoginDouYinFacade::LoginAndRegisterWithMiniGame($collection,$validated['ip']);

		return $result;

	}

	/**
	 * 抖音小程序登录
	 *
	 * @param  Request $request
	 */
	public function getOpenIdByCodeWithMiniProgram(Request $request)
	{
		$result = code(\config('phone_code.DouyinMiniProgramLoginError'));

        $requestDTO = (new GetOpenIdByCodeWithMiniProgramDTO())->validate($request->all());

		$collection = DouYinFacade::getOpenIdByCodeWithMiniProgram($requestDTO);

		$result = PhoneLoginDouYinFacade::LoginAndRegisterWithMiniProgram($collection,$validated['ip']);

		return $result;
	}
}
