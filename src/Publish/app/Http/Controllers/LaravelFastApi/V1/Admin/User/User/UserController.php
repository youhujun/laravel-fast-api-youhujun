<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 14:54:26
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 15:48:14
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserController.php
 */



namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\FormatTime;

use App\Rules\Common\Phone;
use App\Rules\Common\IdNumber;
use App\Rules\LaravelFastApi\V1\Admin\User\User\CheckSex;
use App\Rules\LaravelFastApi\V1\Admin\Login\Password;

use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserFacade;

class UserController extends Controller
{

    public function test()
    {
        echo 'UserController test';
    }

   /**
    * 获取用户列表
    *
    * @param  Request $request
    */
    public function getUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    // 当前页
                    'currentPage' => ['bail', 'nullable', new Numeric],
                    // 每页条数
                    'pageSize' => ['bail', 'nullable', new Numeric],
                    // 排序类型
                    'sortType' => ['bail', new Required, new Numeric],
                    // 是否导出
                    'isExport' => ['bail', 'nullable', new Numeric],
                    // 导出类型
                    'exportType' => ['bail', 'nullable', new Numeric],
                    // 查找内容
                    'find' => ['bail', 'nullable', new CheckString],
                    // 查找内容项下标
                    'findSelectIndex' => ['bail', 'nullable', new Numeric],
                    // 时间范围 添加时间
                    'timeRange' => ['bail', 'nullable', new CheckArray],
                    // 是否禁用
                    'switch' => ['bail', 'nullable', new Numeric],
                    // 状态 0未申请 10未认证 20申请中  30通过 40拒绝
                    'real_auth_status' => ['bail', 'nullable', new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            //p($validated);die;

            $result = AdminUserFacade::getUser(f($validated));
        }

        return $result;
    }


    /**
     * 添加用户
     *
     * @param AddUserRequest $request
     * @return void
     */
    public function addUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'source_user_id' => ['bail', new Required, new Numeric],
                    'phone' => ['bail', new Required, new CheckString, new Phone, new CheckUnique('users', 'phone')],
                    "password" => ['bail', new Required, new CheckString, new CheckBetween(6, 15), new Password],
                    "nick_name" => ['bail', 'nullable', new CheckString, new CheckBetween(1, 60)],
                    "sex" => ['bail', 'nullable', new Numeric, new CheckSex],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['source_user_id'])) {
                throw new RuleException('RuleRequiredError', 'source_user_id');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            if (!isset($validated['password'])) {
                throw new RuleException('RuleRequiredError', 'password');
            }

           //p($validated);die;

            $result = AdminUserFacade::addUser($validated,  $admin);
        }

        return $result;
    }

    /**
     * 禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail', new Required, new Numeric],
                    'switch' => ['bail', new Required, new Numeric],

                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['switch'])) {
                throw new RuleException('RuleRequiredError', 'switch');
            }

            $result = AdminUserFacade::disableUser($validated, $admin);
        }

        return $result;
    }

    /**
     * 批量禁用用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDisableUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'selectId' => ['bail', new Required, new CheckArray],
                    'switch' => ['bail', new Required, new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

           if (!isset($validated['switch'])) {
                throw new RuleException('RuleRequiredError', 'switch');
           }

            $result = AdminUserFacade::multipleDisableUser($validated, $admin);
        }

        return $result;
    }

    /**
     * 删除用户
     *
     * @param Request $request
     * @return void
     */
    public function deleteUser(Request $request)
    {

        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail', new Required, new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            $result = AdminUserFacade::deleteUser($validated, $admin);
        }

        return $result;
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUser(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'selectId' => ['bail', new Required, new CheckArray],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
           }

            $result = AdminUserFacade::multipleDeleteUser($validated, $admin);
        }

        return $result;
    }


}
