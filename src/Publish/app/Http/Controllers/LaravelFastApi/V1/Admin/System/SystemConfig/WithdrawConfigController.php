<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 12:47:00
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 15:34:39
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfigController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig;

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

use App\Facades\LaravelFastApi\V1\Admin\System\SystemConfig\AdminWithdrawConfigFacade;

class WithdrawConfigController extends Controller
{
	 /**
     * 获取系统提现配置
     */
    public function getWithdrawConfig(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    //'type'=>['bail','nullable',new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

			//p($validated);die;

            $result =  AdminWithdrawConfigFacade::getWithdrawConfig(f($validated),$admin);
        }

        return $result;
    }

	/**	
	 * 更新系统提现配置
	 */
	public function updateWithdrawConfig(Request $request)
	{
		$result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
					'id'=>['bail',new Required,new Numeric],
                    //'item_name'=>['bail',new Required,new CheckString],
                    'item_value'=>['bail',new Required,new CheckString],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['item_value'])) {
                throw new RuleException('RuleRequiredError', 'item_value');
            }

			//p($validated);die;

            $result =  AdminWithdrawConfigFacade::updateWithdrawConfig(f($validated),$admin);
        }

        return $result;

	}

    
}
