<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-02 18:12:03
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-02 19:53:35
 * @FilePath: \app\Http\Controllers\Phone\System\RegionController.php
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

use App\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacade;

class RegionController extends Controller
{
	
    /**
     * 手机替换
     *
     * @param Request $request
     * @return void
     */
    public function getRegionById(Request $request)
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

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

			//p($validated);die;

			if(count($validated))
			{
				$result = PhoneRegionFacade::getRegionById(f($validated),$user);
			}
        }

        return $result;
    }

	/**
	 * 获取树形地区
	 *
	 * @param  Request $request
	 */
	public function getTreeRegions(Request $request)
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

			$result = PhoneRegionFacade::getTreeRegions(f($validated),$user);
			
        }

        return $result;
	}
}
