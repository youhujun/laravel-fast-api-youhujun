<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-17 10:21:04
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:30:20
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level\UserLevelController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckUnique;

use App\Facades\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacade;

class UserLevelController extends Controller
{
    /**
     * 获取默认选项
     */
    public function defaultUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'type'=>['bail','nullable',new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            $result =  AdminUserLevelFacade::defaultUserLevel($validated);
        }

        return $result;
    }

    /**
     * 查找选项
     *
     * @param Request $request
     * @return void
     */
    public function findUserLevel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
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

            $result = AdminUserLevelFacade::findUserLevel(f($validated));
        }

        return $result;
    }


    /**
     * 获取用户级别列表
     */
    public function getUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

       $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail','nullable',new CheckString],
                    'findSelectIndex'=>['bail','nullable',new Numeric],
                    'timeRange'=>['bail','nullable',new CheckArray],
                    'sortType'=>['bail',new Required,new Numeric],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

            $result = AdminUserLevelFacade::getUserLevel(f($validated));
        }

        return $result;
    }

    /**
     * 添加用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function addUserLevel(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'sort'=>['bail',new Required,new Numeric],
                    'level_name'=>['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine,new CheckUnique('user_level','level_name')],
                    'level_code'=>['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine,new CheckUnique('user_level','level_code')],
                    'amount'=>['bail','nullable',new Numeric],
                    'background_id'=>['bail','nullable',new Numeric],
                    'note'=>['bail','nullable',new CheckString,new CheckBetween(1,60)],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            if (!isset($validated['level_name'])) {
                throw new RuleException('RuleRequiredError', 'level_name');
            }

            if (!isset($validated['level_code'])) {
                throw new RuleException('RuleRequiredError', 'level_code');
            }

            $result = AdminUserLevelFacade::addUserLevel(f($validated),$user);
        }

        return $result;
    }

    /**
     * 修改用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function updateUserLevel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric],
                    'level_name'=>['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine,new CheckUnique('user_level','level_name',$id)],
                    'level_code'=>['bail',new Required,new CheckString,new CheckBetween(1,30),new ChineseCodeNumberLine,new CheckUnique('user_level','level_code',$id)],
                    'amount'=>['bail','nullable',new Numeric],
                    'background_id'=>['bail','nullable',new Numeric],
                    'note'=>['bail','nullable',new CheckString,new CheckBetween(1,60)],
                ],
                []
            );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['sort'])) {
               throw new RuleException('RuleRequiredError', 'sort');
           }

           if (!isset($validated['level_name'])) {
               throw new RuleException('RuleRequiredError', 'level_name');
           }

           if (!isset($validated['level_code'])) {
               throw new RuleException('RuleRequiredError', 'level_code');
           }

           $result =AdminUserLevelFacade::updateUserLevel(f($validated),$user);
        }

        return $result;
    }

    /**
     * 删除用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLevel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            $result = AdminUserLevelFacade::deleteUserLevel($validated,$user);
        }

        return $result;
    }

    /**
     * 批量删除用户级别列表
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteUserLevel(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
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

            $result = AdminUserLevelFacade::multipleDeleteUserLevel(f($validated),$user);
        }

        return $result;
    }


    /**
     * 添加用户级别配置项值
     *
     * @param Request $request
     * @return void
     */
    public function addUserLevelItemUnion(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric],
                    'user_level_id'=>['bail',new Required,new Numeric],
                    'level_item_id'=>['bail',new Required,new Numeric],
                    'value'=>['bail',new Required,new Numeric],
                    'value_type'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if(isset($validated['id']) && $validated['id'] === 0)
            {
                unset($validated['id']);
            }

            $result = AdminUserLevelFacade::addUserLevelItemUnion(f($validated),$user);
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateUserLevelItemUnion(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'sort'=>['bail',new Required,new Numeric],
                    'user_level_id'=>['bail',new Required,new Numeric],
                    'level_item_id'=>['bail',new Required,new Numeric],
                    'value'=>['bail',new Required,new Numeric],
                    'value_type'=>['bail',new Required,new Numeric]
                ],
                []
            );

           $validated = $validator->validated();

           $result =AdminUserLevelFacade::updateUserLevelItemUnion(f($validated),$user);
        }

        return $result;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserLevelItemUnion(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('admin-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            $result = AdminUserLevelFacade::deleteUserLevelItemUnion($validated,$user);
        }

        return $result;
    }

}
