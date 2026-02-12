<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-02 10:42:01
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserRealAuthController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

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

use App\Rules\Common\IdNumber;

use App\Exceptions\Common\RuleException;

use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacade;

class UserRealAuthController extends Controller
{

    /**
     * 获取用户实名认证申请
     *
     * @param Request $request
     * @return void
     */
    public function getUserRealAuthApply(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id' => ['bail', new Required, new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            $result = AdminUserRealAuthFacade::getUserRealAuthApply(f($validated), $admin);
        }

        return $result;
    }
    /**
     * 实名认证审核
     *
     * @param Request $request
     * @return void
     */
    public function realAuthUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail', new Required, new Numeric],
                    'user_id' => ['bail', new Required, new Numeric],
                    'is_real_auth' => ['bail', new Required, new Numeric],
                    'refuse_info' => ['bail', 'nullable', new CheckString, new CheckBetween(0, 64)],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['is_real_auth'])) {
                throw new RuleException('RuleRequiredError', 'is_real_auth');
            }

            $result = AdminUserRealAuthFacade::realAuthUser(f($validated), $admin);
        }

        return $result;
    }

    /**
     * 修改用户身份证号
     *
     * @param Request $request
     * @return void
     */
    public function updateUserIdNumber(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $admin = Auth::guard('admin_token')->user();

        $id = checkId($request->input('id'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id'=>['bail',new Required,new Numeric],
                    'id_number'=>['bail', 'nullable', new CheckString, new CheckBetween(15, 18), new IdNumber, new CheckUnique('user_info', 'user_id',$id)],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            $result =  AdminUserRealAuthFacade::updateUserIdNumber(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 获取用户身份证
     *
     * @param Request $request
     * @return void
     */
    public function getUserIdCard(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'user_id' => ['bail', new Required, new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

			if(!isset($validated['user_id'])){
				throw new RuleException('RuleRequiredError','user_id');
			}

            $result = AdminUserRealAuthFacade::getUserIdCard(f($validated), $admin);
        }

        return $result;
    }

    /**
    * 设置用户身份证照片
    *
    * @param Request $request
    * @return void
    */
    public function setUserIdCard(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'user_id' => ['bail', new Required, new Numeric],
                    'id_card_front_id' => ['bail', new Required, new Numeric],
                    'id_card_back_id' => ['bail', new Required, new Numeric],
                    'sort' => ['bail', 'nullable', new Numeric],
               ],
               []
           );

            $validated = $validator->validated();

            $result = AdminUserRealAuthFacade::setUserIdCard(f($validated), $admin);
        }

        return $result;
    }




}
