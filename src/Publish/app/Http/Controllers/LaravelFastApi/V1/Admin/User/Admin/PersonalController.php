<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-08 14:09:11
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 15:02:46
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\PersonalController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin;

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

use App\Rules\Common\Phone;

use App\Exceptions\Common\RuleException;



use App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade;

class PersonalController extends Controller
{
	/**
     * 确认修改头像
     */
    public function updateAvatar(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    
                ],
                []
            );

            $validated = $validator->validated();

            $result =  AdminPersonalFacade::updateAvatar(f($validated),$admin);
        }

        return $result;
    }

	/**
     * 确认修改头像
     */
    public function updatePhone(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                     'phone'=>['bail',new Required,new Phone,new CheckUnique('admin','phone')]
                ],
                []
            );

            $validated = $validator->validated();

			if(!isset($validated['phone'])){
				throw new RuleException('RuleRequiredError','phone');
			}

			//p($validated);die;

            $result =  AdminPersonalFacade::updatePhone(f($validated),$admin);
        }

        return $result;
    }

	/**
     * 确认修改头像
     */
    public function updatePassword(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'password'=>['bail',new Required,new CheckString],
                    'repass'=>['bail',new Required,new CheckString],
                ],
                []
            );

            $validated = $validator->validated();

			if(!isset($validated['password'])){

				throw new RuleException('RuleRequiredError','password');
			}

			if(!isset($validated['repass'])){

				throw new RuleException('RuleRequiredError','repass');
			}

			// p($validated);die;

            $result =  AdminPersonalFacade::updatePassword(f($validated),$admin);
        }

        return $result;
    }

   
}
