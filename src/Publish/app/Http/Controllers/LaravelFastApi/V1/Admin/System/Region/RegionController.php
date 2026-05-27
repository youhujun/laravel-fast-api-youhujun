<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-12 09:24:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-08 16:08:14
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\System\Region\RegionController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\System\Region;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\AddRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\UpdateRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\MoveRegionDTO;
use App\DTOs\LaravelFastApi\V1\Admin\System\Region\DeleteRegionDTO;
use App\Facades\LaravelFastApi\V1\Admin\System\Region\AdminRegionFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\Region\AdminRegionFacadeService
 */
class RegionController extends Controller
{
    /**
     * 获取所有地址
     *
     * @return void
     */
    public function getAllRegion()
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result = AdminRegionFacade::getAllRegion();
        }

        return $result;
    }

    /**
     * 获取树形地址
     *
     * @return void
     */
    public function getTreeRegion()
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $result = AdminRegionFacade::getTreeRegion();
        }

        return $result;
    }


    /**
    * 添加顶级/下级地区
    *
    * @param Request $request
    * @return void
    */
    public function addRegion(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new AddRegionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRegionFacade::addRegion($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改地区
     *
     * @param Request $request
     * @return void
     */
    public function updateRegion(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new UpdateRegionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRegionFacade::updateRegion($requestDTO, $adminObject);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveRegion(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new MoveRegionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRegionFacade::moveRegion($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除地区
     *
     * @param Request $request
     * @return void
     */
    public function deleteRegion(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('super-role')) {
            $requestDTO = (new DeleteRegionDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminRegionFacade::deleteRegion($requestDTO, $adminObject);
        }

        return $result;
    }
}
