<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 09:55:14
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 11:18:41
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\Login\RegisterController.php
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

use App\Rules\LaravelFastApi\V1\Phone\RegisterPhone;

use App\Exceptions\Common\RuleException;

use App\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacade;

class RegisterController extends Controller
{
    /**
     * 发送用户注册码
     */
    public function sendPhoneRegisterCode(Request $request)
    {

        $result = code(\config('phone.sendUserRegisterCodeError'));

        $validator = Validator::make(
            $request->all(),
            [
               'phone'=>['bail',new Required,new CheckString,new RegisterPhone],
            ],
            [ ]
        );

        $validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}
        //p($validated);die;
        $result = PhoneRegisterFacade::sendUserRegisterCode($validated);

        return $result;
    }

    /**
     * 用户注册
     */
    public function userRegister(Request $request)
    {
        $result = code(\config('phone.userRegisterError'));

        $validator = Validator::make(
            $request->all(),
            [
               'phone'=>['bail',new Required,new CheckString,new RegisterPhone],
               'registerCode'=>['bail',new Required,new CheckString],
               'password'=>['bail',new Required,new CheckString],
               'inviteId'=>['bail','nullable',new CheckString],
               'inviteCode'=>['bail','nullable',new CheckString]
            ],
            [ ]
        );

        $validated = $validator->validated();

		if(!isset($validated['phone'])){
			throw new RuleException('RuleRequiredError','phone');
		}

		if(!isset($validated['registerCode'])){
			throw new RuleException('RuleRequiredError','registerCode');
		}

		if(!isset($validated['password'])){
			throw new RuleException('RuleRequiredError','password');
		}

        // p($validated);die;

        $result = PhoneRegisterFacade::userRegister(f($validated));

        return $result;
    }

}
