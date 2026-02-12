<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 22:41:13
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-20 23:30:46
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Login;

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

use App\Rules\LaravelFastApi\V1\Admin\Login\LoginAdminAccount;

use App\Facades\LaravelFastApi\V1\Admin\Login\AdminLoginFacade;

class LoginController extends Controller
{
		 /**
     * 获取默认选项
     */
    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'username' => ['bail',new Required,new CheckString,new CheckBetween(4,30),new LoginAdminAccount],
                'password' =>[ 'bail',new Required,new CheckString,new CheckBetween(6,12)],
            ],
            []
        );

        $validated = $validator->validated();

        if (!isset($validated['username'])) {
            throw new RuleException('RuleRequiredError', 'username');
        }

        if (!isset($validated['password'])) {
            throw new RuleException('RuleRequiredError', 'password');
        }

        //p($validated);die;

        $result = AdminLoginFacade::authLogin($validated);

        return $result;
    }

	/**
     * 管理员退出
     *
     * @param  Request $request
     */
    public function logout(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminLoginFacade::logout($admin);
        }
        return $result;
    }

   

	



}
