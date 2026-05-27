<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 14:01:07
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\Role\RoleController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\AddRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\UpdateRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\MoveRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\DeleteRoleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Role\ResetRolePermissionDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacadeService
 */
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new AddRoleDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminRoleFacade::addRole($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));


        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new UpdateRoleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRoleFacade::updateRole($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MoveRoleDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminRoleFacade::moveRole($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteRoleDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminRoleFacade::deleteRole($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new ResetRolePermissionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRoleFacade::resetRolePermission($requestDTO, $adminObject);
        }

        return $result;
    }
}
