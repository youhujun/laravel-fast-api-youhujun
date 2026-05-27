<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-28 08:06:57
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 13:49:52
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\LabelController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\AddLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\UpdateLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\MoveLabelDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Label\DeleteLabelDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacadeService
 */
class LabelController extends Controller
{
    /**
      * 获取树形地址
      *
      * @return void
      */
    public function getTreeLabel()
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $result = AdminLabelFacade::getTreeLabel();
        }

        return  $result;
    }

    /**
    * 添加顶级/下级标签
    *
    * @param Request $request
    * @return void
    */
    public function addLabel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new AddLabelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLabelFacade::addLabel($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 更新标签
     *
     * @param Request $request
     * @return void
     */
    public function updateLabel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new UpdateLabelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLabelFacade::updateLabel($requestDTO, $userObject);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveLabel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('super-role')) {
            $requestDTO = (new MoveLabelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLabelFacade::moveLabel($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 删除标签
     *
     * @param Request $request
     * @return void
     */
    public function deleteLabel(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new DeleteLabelDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            //p($validated);die;
            $result = AdminLabelFacade::deleteLabel($requestDTO, $userObject);
        }

        return $result;
    }
}
