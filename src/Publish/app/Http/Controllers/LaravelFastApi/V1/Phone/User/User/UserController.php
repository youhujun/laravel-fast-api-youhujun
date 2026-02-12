<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 17:35:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 09:03:38
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\User\User\UserController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\User\User;

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

use App\Rules\Common\IdNumber;
use App\Rules\Common\Phone;

use App\Facades\LaravelFastApi\V1\Phone\User\User\PhoneUserFacade;

class UserController extends Controller
{
	 /**
     * 获取默认选项
     */
    public function realAuthApply(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        $id = $user->id;

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'real_name'=>['bail','required',new CheckString],
                    'id_number'=>['bail','required',new IdNumber,new CheckUnique('user_info','id_number',$id)],
                    'id_card_front_id'=>['bail','required',new Numeric],
                    'id_card_back_id'=>['bail','required',new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['real_name'])) {
                throw new RuleException('RuleRequiredError', 'real_name');
            }

            if (!isset($validated['id_number'])) {
                throw new RuleException('RuleRequiredError', 'id_number');
            }

            if (!isset($validated['id_card_front_id'])) {
                throw new RuleException('RuleRequiredError', 'id_card_front_id');
            }

            if (!isset($validated['id_card_back_id'])) {
                throw new RuleException('RuleRequiredError', 'id_card_back_id');
            }

            //p($validated);die;

            $result =  PhoneUserFacade::realAuthApply(f($validated),$user);
        }

        return $result;
    }

}
