<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 09:24:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-09-26 16:53:51
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
use App\Rules\Pub\LetterNumberUnderLine;


use App\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade;

class PermissionController extends Controller
{

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
             $result =  AdminPermissionFacade::getTreePermission($admin);
        }

        return $result;
    }

	/**
	 * 获取自己选项
	 *
	 * @param  Request $request
	 */
	public function getChildrenOptions(Request $request)
	{

		$admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));
		
		$validator = Validator::make(
			$request->all(),
			[
				"onlyParent"=>['bail',new Required,new Numeric],
			],
			[]
		);

        $validated = $validator->validated();

        if (!isset($validated['onlyParent'])) {
            throw new RuleException('RuleRequiredError', 'onlyParent');
        }

		//p($validated);

		if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result =  AdminPermissionFacade::getChildrenOptions($admin,$validated);
        }

        return $result;


	}

	/**
	 * 获取单个菜单表单数据
	 *
	 * @param  Request $request
	 */
	public function getSingleMenuForm(Request $request)
	{
		$admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));
		
		$validator = Validator::make(
			$request->all(),
			[
				"id"=>['bail',new Required,new Numeric],
			],
			[]
		);

        $validated = $validator->validated();

        if (!isset($validated['onlyParent'])) {
            throw new RuleException('RuleRequiredError', 'onlyParent');
        }

		//p($validated);die;

		if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result =  AdminPermissionFacade::getSingleMenuForm($admin,$validated);
        }

        return $result;
	}

	/**
     * 获取树形编辑菜单
     * @return void
     */
    public function getTreeMenu()
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
        {
             $result =  AdminPermissionFacade::getTreeMenu($admin);
        }

        return $result;
    }


    /**
     * 添加菜单
     *
     * @param Request $result
     * @return void
     */
    public function addMenu(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('develop-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    //"id"=>['bail',new Required,new Numeric],
					"parent_id"=>['bail',new Required,new Numeric],
					"deep"=>['bail',new Required,new Numeric],
					"type"=>['bail',new Required,new Numeric],
					"route_name"=>['bail',new Required,new CheckString],
					"route_path"=>['bail',new Required,new CheckString],
					"component"=>['bail',new Required,new CheckString],
					"hidden"=>['bail',new Required,new Numeric],
					"always_show"=>['bail',new Required,new Numeric],
					"perm"=>['bail','nullable',new CheckString],
					"switch"=>['bail','nullable',new Numeric],
					"sort"=>['bail','nullable',new Numeric],
					//meta开始
					"icon"=>['bail','nullable',new CheckString],
					"title"=>['bail',new Required,new CheckString],
					"cache"=>['bail','nullable',new Numeric],
					"affix"=>['bail','nullable',new Numeric],
					"breadcrumb"=>['bail','nullable',new Numeric],
					"active_menu"=>['bail','nullable',new CheckString],
					//meta结束
					"redirect"=>['bail','nullable',new CheckString],
					"params"=>['bail','nullable',new CheckArray],
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

		//p($validated);die;

            $result = AdminPermissionFacade::addMenu(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 更新菜单
     *
     * @param AddMenuRequest $request
     * @return void
     */
    public function updateMenu(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('develop-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
					"id"=>['bail',new Required,new Numeric],
					"parent_id"=>['bail',new Required,new Numeric],
					"deep"=>['bail',new Required,new Numeric],
					"type"=>['bail',new Required,new Numeric],
					"route_name"=>['bail',new Required,new CheckString],
					"route_path"=>['bail',new Required,new CheckString],
					"component"=>['bail',new Required,new CheckString],
					"hidden"=>['bail',new Required,new Numeric],
					"always_show"=>['bail',new Required,new Numeric],
					"perm"=>['bail','nullable',new CheckString],
					"switch"=>['bail','nullable',new Numeric],
					"sort"=>['bail','nullable',new Numeric],
					//meta开始
					"icon"=>['bail','nullable',new CheckString],
					"title"=>['bail',new Required,new CheckString],
					"cache"=>['bail','nullable',new Numeric],
					"affix"=>['bail','nullable',new Numeric],
					"breadcrumb"=>['bail','nullable',new Numeric],
					"active_menu"=>['bail','nullable',new CheckString],
					"redirect"=>['bail','nullable',new CheckString],
					//meta结束
					"params"=>['bail','nullable',new CheckArray],
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

            if (!isset($validated['deep'])) {
                throw new RuleException('RuleRequiredError', 'deep');
            }

            if (!isset($validated['type'])) {
                throw new RuleException('RuleRequiredError', 'type');
            }

            if (!isset($validated['route_name'])) {
                throw new RuleException('RuleRequiredError', 'route_name');
            }

            if (!isset($validated['route_path'])) {
                throw new RuleException('RuleRequiredError', 'route_path');
            }

            if (!isset($validated['component'])) {
                throw new RuleException('RuleRequiredError', 'component');
            }

            if (!isset($validated['hidden'])) {
                throw new RuleException('RuleRequiredError', 'hidden');
            }

            if (!isset($validated['always_show'])) {
                throw new RuleException('RuleRequiredError', 'always_show');
            }

            if (!isset($validated['title'])) {
                throw new RuleException('RuleRequiredError', 'title');
            }

            //p($validated);die;

            $result = AdminPermissionFacade::updateMenu(f($validated),$admin);
        }

        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveMenu(Request $request)
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
                    'dropType' => ['bail',new Required,new CheckString],
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

            $result =  AdminPermissionFacade::moveMenu(f($validated),$admin);
        }

       return $result;
    }

    /**
     * 删除菜单
     *
     * @param Request $request
     * @return void
     */
    public function deleteMenu(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('develop-role'))
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

            $result = AdminPermissionFacade::deleteMenu(f($validated),$user);
        }

        return $result;
    }

    /**
     * 禁用或者开启菜单
     *
     * @param Request $request
     * @return void
     */
    public function switchMenu(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if(Gate::forUser($user)->allows('develop-role'))
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'switch'=>['bail',new Required,new Numeric],
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

		//p($validated);die;

            $result = AdminPermissionFacade::switchMenu(f($validated),$user);

        }

        return $result;


    }
}
