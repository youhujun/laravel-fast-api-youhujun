<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-04 16:02:42
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-10-02 16:35:11
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\UserController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My;

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
use App\Rules\Common\Phone;

use App\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacade;
use App\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacade;

class UserController extends Controller
{
	

    /**
     * 手机替换
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
                ],
                []
            );

            $validated = $validator->validated();

			//p($validated);die;

            $result = PhoneUserInfoFacade::getUserInfo(f($validated),$user);
        }

        return $result;
    }

	/**
	 * 上传用户头像
	 */
	public function uploadUserAvatar(Request $request)
	{
		$result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
          
            if($request->hasFile('picture'))
            {
                $picture = $request->file('picture');

                // PhonePictureFacade::test(); die;
                //p($picture);die;
                $result = PhonePictureFacade::uploadUserAvatar($user, $picture);
            }
        }

        return $result;
	}

	/**
	 * 修改用户昵称
	 *
	 * @param  Request $request
	 */
	public function updateUserNickName(Request $request)
	{
		$result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
					'nick_name'=>['bail',new Required,new CheckString]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['nick_name'])) {
                throw new RuleException('RuleRequiredError', 'nick_name');
            }

			//p($validated);die;

            $result = PhoneUserInfoFacade::updateUserNickName(f($validated),$user);
        }

        return $result;
	}

	/**
	 * 修改用户手机号
	 *
	 * @param  Request $request
	 */
	public function updateUserPhone(Request $request)
	{
		$result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
					'phone'=>['bail',new Required,new Phone,new CheckUnique('users','phone',$user->id)]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

			//p($validated);die;

            $result = PhoneUserInfoFacade::updateUserPhone(f($validated),$user);
        }

        return $result;
	}

}
