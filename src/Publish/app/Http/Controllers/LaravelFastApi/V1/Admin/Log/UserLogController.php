<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 16:18:56
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:43:08
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Log\UserLogController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\LetterNumberUnderLine;
use App\Exceptions\Common\RuleException;

use App\Facades\LaravelFastApi\V1\Admin\Log\AdminUserLogFacade;

class UserLogController extends Controller
{
     /*
     * 获取登录日志
     *
     * @param Request $request
     * @return void
     */
    public function getUserLoginLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'user_id'=>['bail','nullable',new CheckBetween(0,50),new Numeric],
                  'timeRange'=>['bail','nullable',new CheckArray],
                  'status'=>['bail','nullable',new Numeric],
                  'sortType'=>['bail','nullable',new Numeric],
                  'currentPage'=>['bail','nullable',new Numeric],
                  'pageSize'=>['bail','nullable',new Numeric]
               ],
               []
           );

           $validated = $validator->validated();

           // p($validated);die;

           $result = AdminUserLogFacade::getUserLoginLog(f($validated),$admin);

        }

        return $result;
    }

    /**
     * 删除日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLoginLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'id'=>['bail',new Required,new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

            $result = AdminUserLogFacade::deleteUserLoginLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserLoginLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                   'selectId'=>['bail',new Required,new CheckArray],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['selectId'])) {
               throw new RuleException('RuleRequiredError', 'selectId');
           }

            $result = AdminUserLogFacade::multipleDeleteUserLoginLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 获取事件日志
     *
     * @return void
     */
    public function getUserEventLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                    'user_id'=>['bail','nullable',new CheckBetween(0,50),new Numeric],
                    'timeRange'=>['bail','nullable',new CheckArray],
                    'eventType'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail','nullable',new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric]
               ],
               []
           );

           $validated = $validator->validated();

            $result = AdminUserLogFacade::getUserEventLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserEventLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                  'id'=>['bail',new Required,new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           $result = AdminUserLogFacade::deleteUserEventLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserEventLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                   'selectId'=>['bail',new Required,new CheckArray],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['selectId'])) {
               throw new RuleException('RuleRequiredError', 'selectId');
           }

           $result = AdminUserLogFacade::multipleDeleteUserEventLog($validated,$admin);
        }

        return $result;
    }

}
