<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-06-20 23:41:34
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-21 01:00:23
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginAfterController.php
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

use App\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacade;

use App\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade;

use App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacade;

class LoginAfterController extends Controller
{
	 /**
     * 获取登录管理员信息
     */
    public function getAdminInfo(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $result = AdminLoginFacade::getAdminInfo($admin);
        }

        return $result;

    }

	/**
     * 获取树形权限菜单
     * @return void
     */
    public function getTreePermission(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result = AdminPermissionFacade::getTreePermission($admin);
        }

        return $result;
    }

	/**
     * 获取所有提示配置
     */
    public function getAllSystemVoiceConfig(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                ],
                [ ]
            );

            $validated = $validator->validated();

            //p($validated);die;

            $result = AdminVoiceConfigFacade::getAllSystemVoiceConfig(f($validated),$admin);
        }

        return $result;
    }

}
