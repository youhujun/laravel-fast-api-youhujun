<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 09:24:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 10:57:53
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\AddMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\UpdateMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\DeleteMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\MoveMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\SwitchMenuDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\GetChildrenOptionsDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Permission\GetSingleMenuFormDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacadeService
 */
class PermissionController extends Controller
{
    /**
     * 获取树形权限菜单
     * @return void
     */
    public function getTreePermission(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result =  AdminPermissionFacade::getTreePermission($adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result = AdminPermissionFacade::getChildrenOptions($adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetSingleMenuFormDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::getSingleMenuForm($adminObject, $requestDTO);
        }

        return $result;
    }

    /**
     * 获取树形编辑菜单
     * @return void
     */
    public function getTreeMenu()
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result =  AdminPermissionFacade::getTreeMenu($adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('develop-role')) {
            $requestDTO = (new AddMenuDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::addMenu($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('develop-role')) {
            $requestDTO = (new UpdateMenuDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::updateMenu($requestDTO, $adminObject);
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
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MoveMenuDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::moveMenu($requestDTO, $adminObject);
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
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('develop-role')) {
            $requestDTO = (new DeleteMenuDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::deleteMenu($requestDTO, $userObject);
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
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('develop-role')) {
            $requestDTO = (new SwitchMenuDTO())->validate($request->all());
            // p($requestDTO);
            // die;
            $result = AdminPermissionFacade::switchMenu($requestDTO, $userObject);
        }

        return $result;
    }
}
