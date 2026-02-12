<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-03 16:42:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-22 17:50:34
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\System\GoodsClassController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\System;

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

use App\Facades\LaravelFastApi\V1\Phone\System\GoodsClass\PhoneGoodsClassFacade;

class GoodsClassController extends Controller
{
	
	/**
	 * 获取产品分类通过id
	 *
	 * @param  Request $request
	 */
	public function getGoodsClassById(Request $request)
	{
		$result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'parent_id'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

			//p($validated);die;

			if(count($validated))
			{
				$result = PhoneGoodsClassFacade::getGoodsClassById(f($validated),$user);
			}
        }

        return $result;
	}



    /**
     * 手机替换
     *
     * @param Request $request
     * @return void
     */
    public function getTreeGoodsClass(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail',new Required],
                    'type'=>['bail','nullable',new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['find'])) {
                throw new RuleException('RuleRequiredError', 'find');
            }

            $result = PhoneGoodsClassFacade::getTreeGoodsClass(f($validated),$user);
        }

        return $result;
    }
}


