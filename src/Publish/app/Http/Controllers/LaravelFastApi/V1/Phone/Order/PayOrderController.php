<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 08:01:25
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-26 19:58:26
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\Order\PayOrderController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\Order;

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

use App\Facades\LaravelFastApi\V1\Phone\Pay\PhonePayFacade;

class PayOrderController extends Controller
{
	 /**
     * 获取默认选项
     */
    public function testPayOrder(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'order_id'=>['bail','nullable',new Numeric],
                    'real_pay_amount'=>['bail','nullable',new Numeric],
                    'pay_type'=>['bail','nullable',new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            //p($validated);die;
            //PhonePayFacade::test();die;
            $result =  PhonePayFacade::testPayOrder(f($validated),$user);
        }

        return $result;
    }


}
