<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 16:18:56
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-10 16:45:23
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController.php
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

use App\Facades\LaravelFastApi\V1\Admin\Log\AdminLogFacade;

class AdminLogController extends Controller
{

    /**
     * 获取登录日志
     *
     * @param Request $request
     * @return void
     */
    public function getAdminLoginLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                'admin_id'=>['bail','nullable',new CheckBetween(0,50),new Numeric],
                'timeRange'=>['bail','nullable',new CheckArray],
                'status'=>['bail','nullable',new Numeric],
                'sortType'=>['bail','nullable',new Numeric],
                'currentPage'=>['bail','nullable',new Numeric],
                'pageSize'=>['bail','nullable',new Numeric]
               ],
               []
           );

           $validated = $validator->validated();

           //p($validated);die;

           $result = AdminLogFacade::getAdminLoginLog($validated,$admin);

        }

        return $result;
    }

    /**
     * 删除日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdminLoginLog(Request $request)
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

           $result = AdminLogFacade::deleteAdminLoginLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdminLoginLog(Request $request)
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

            $result = AdminLogFacade::multipleDeleteAdminLoginLog($validated,$admin);
        }

        return $result;
    }

        /**
     * 获取事件日志
     *
     * @return void
     */
    public function getAdminEventLog(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
               [
                    'admin_id'=>['bail','nullable',new CheckBetween(0,50),new Numeric],
                    'timeRange'=>['bail','nullable',new CheckArray],
                    'eventType'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail','nullable',new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric]
               ],
               []
           );

           $validated = $validator->validated();

            $result = AdminLogFacade::getAdminEventLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 删除事件日志
     *
     * @param Request $request
     * @return void
     */
    public function deleteAdminEventLog(Request $request)
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

            $result = AdminLogFacade::deleteAdminEventLog($validated,$admin);
        }

        return $result;
    }

    /**
     * 多选删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteAdminEventLog(Request $request)
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

            $result = AdminLogFacade::multipleDeleteAdminEventLog($validated,$admin);
        }

        return $result;
    }
}
