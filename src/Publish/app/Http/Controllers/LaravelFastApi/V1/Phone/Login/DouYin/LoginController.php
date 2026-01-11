<?php
/*
 * @Descripttion: 抖音开发业务用
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-24 14:44:48
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 18:29:46
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin\LoginController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin;

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

use App\Facade\Pub\V1\DouYin\DouYinFacade;

use App\Facade\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacade;


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

         $validator = Validator::make(
            $request->all(),
            [
				'appid'=>['bail',new Required,new CheckString],
                'code'=>['bail',new Required,new CheckString],
                'anonymousCode'=>['bail','nullable',new CheckString], 
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

		$validated['ip'] = $request->getClientIp();

		$collection = DouYinFacade::getOpenIdByCodeWithMiniGame($validated);

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

        $validator = Validator::make(
            $request->all(),
            [
				'appid'=>['bail',new Required,new CheckString],
                'code'=>['bail',new Required,new CheckString],
                'anonymousCode'=>['bail','nullable',new CheckString], 
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

		$validated['ip'] = $request->getClientIp();

		$collection = DouYinFacade::getOpenIdByCodeWithMiniProgram($validated);

		$result = PhoneLoginDouYinFacade::LoginAndRegisterWithMiniProgram($collection,$validated['ip']);

		return $result;
	}
}
