<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 18:13:36
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\Role\RoleController.php
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\CheckArray;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\Required;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckUnique;

use App\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep;

use App\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacade;

class RoleController extends Controller
{

     /**
     * 获取所有的权限菜单
     */
    public function getAllRole()
    {
        return AdminRoleFacade::getAllData();
    }

    /**
     * 获取树形权限菜单
     *
     * @return void
     */
    public function getTreeRole()
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result =  AdminRoleFacade::getTreeRole();
        }

        return $result;
    }

    /**
     * 添加顶级/下级角色
     *
     * @param Request $request
     * @return void
     */
    public function addRole(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'parent_id'=>['bail',new Required,new Numeric],
                    'deep' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Required,new Numeric],
                    'role_name'=>['bail',new Required,new CheckString,new CheckBetween(2,10)],
                    'logic_name'=>['bail',new Required,new CheckString,new CheckBetween(3,60)]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

            if (!isset($validated['deep'])) {
                throw new RuleException('RuleRequiredError', 'deep');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            if (!isset($validated['role_name'])) {
                throw new RuleException('RuleRequiredError', 'role_name');
            }

            if (!isset($validated['logic_name'])) {
                throw new RuleException('RuleRequiredError', 'logic_name');
            }

            $result = AdminRoleFacade::addRole(f($validated),$admin);

        }

        return $result;

    }

    /**
     * 修改角色
     *
     * @param Request $request
     * @return void
     */
    public function updateRole(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));


        if(Gate::forUser($admin)->allows('super-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'sort' => ['bail',new Required,new Numeric],
                    'role_name'=>['bail',new Required,new CheckString,new CheckBetween(2,10)],
                    'logic_name'=>['bail',new Required,new CheckString,new CheckBetween(3,60)]
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

            if (!isset($validated['role_name'])) {
                throw new RuleException('RuleRequiredError', 'role_name');
            }

            if (!isset($validated['logic_name'])) {
                throw new RuleException('RuleRequiredError', 'logic_name');
            }

            $result = AdminRoleFacade::updateRole(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveRole(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'parent_id'=>['bail',new Required,new Numeric],
                    'dropType' => ['bail',new Required,new TreeDeep],
                    'sort' => ['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['parent_id'])) {
                throw new RuleException('RuleRequiredError', 'parent_id');
            }

            if (!isset($validated['dropType'])) {
                throw new RuleException('RuleRequiredError', 'dropType');
            }

            if (!isset($validated['sort'])) {
                throw new RuleException('RuleRequiredError', 'sort');
            }

            $result = AdminRoleFacade::moveRole($validated,$admin);
        }

       return $result;
    }
    /**
     * 删除角色
     *
     * @param Request $request
     * @return void
     */
    public function deleteRole(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_delete'=>['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['is_delete'])) {
                throw new RuleException('RuleRequiredError', 'is_delete');
            }

            $result = AdminRoleFacade::deleteRole($validated,$admin);

        }

        return $result;
    }

    /**
     * 重置角色权限
     *
     * @param Request $request
     * @return void
     */
    public function resetRolePermission(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('super-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'role_id'=>['bail',new Required,'integer'],
                    'before_permission'=>['bail','nullable',new CheckArray],
                    'after_permission'=>['bail','nullable',new CheckArray]
                ],
                []
            );

            $validated = $validator->validated();

            //p($validated);die;

            $result = AdminRoleFacade::resetRolePermission($validated ,$admin);
        }

        return $result;
    }
}
