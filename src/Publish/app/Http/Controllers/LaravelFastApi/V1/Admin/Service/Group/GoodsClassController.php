<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:41:09
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 14:58:12
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\GoodsClassController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\GetSingleGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\AddGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\UpdateGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\MoveGoodsClassDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Group\AdminGoodsClassFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminGoodsClassFacadeService
 */
class GoodsClassController extends Controller
{
    /**
    * 获取产品分类
    *
    * @return void
    */
    public function getTreeGoodsClass()
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        //p('here');die;

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result = AdminGoodsClassFacade::getTreeGoodsClass();
        }

        return $result;
    }

    /**
    * 添加产品分类
    *
    * @param Request $request
    * @return void
    */
    public function addGoodsClass(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddGoodsClassDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminGoodsClassFacade::addGoodsClass($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 更新产品分类
     *
     * @param Request $request
     * @return void
     */
    public function updateGoodsClass(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateGoodsClassDTO($id))->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminGoodsClassFacade::updateGoodsClass($requestDTO, $adminObject);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveGoodsClass(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MoveGoodsClassDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminGoodsClassFacade::moveGoodsClass($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除产品分类
     *
     * @param Request $request
     * @return void
     */
    public function deleteGoodsClass(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteGoodsClassDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminGoodsClassFacade::deleteGoodsClass($requestDTO, $adminObject);
        }

        return $result;
    }

    public function getSingleGoodsClass(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new GetSingleGoodsClassDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminGoodsClassFacade::getSingleGoodsClass($requestDTO, $adminObject);
        }

        return $result;
    }
}
