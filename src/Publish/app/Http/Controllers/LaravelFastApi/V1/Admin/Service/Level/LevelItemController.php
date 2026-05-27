<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 16:53:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-09 16:17:56
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level\LevelItemController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Level;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\DefaultLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\FindLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\GetLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\AddLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\UpdateLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\DeleteLevelItemDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Level\LevelItem\MultipleDeleteLevelItemDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Level\AdminLevelItemFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminLevelItemFacadeService
 */
class LevelItemController extends Controller
{
    /**
    * 获取默认级别条件列表
    */
    public function defaultLevelItem(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DefaultLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result =  AdminLevelItemFacade::defaultLevelItem($requestDTO);
        }

        return $result;
    }

    /**
     * 查找级别条件列表
     *
     * @param Request $request
     * @return void
     */
    public function findLevelItem(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new FindLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::findLevelItem($requestDTO);
        }

        return $result;
    }


    /**
     * 获取级别条件列表
     */
    public function getLevelItem(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::getLevelItem($requestDTO);
        }

        return $result;
    }

    /**
     * 添加级别条件
     *
     * @param Request $request
     * @return void
     */
    public function addLevelItem(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::addLevelItem($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改级别条件
     *
     * @param Request $request
     * @return void
     */
    public function updateLevelItem(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateLevelItemDTO($id))->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::updateLevelItem($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除级别条件
     *
     * @param Request $request
     * @return void
     */
    public function deleteLevelItem(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::deleteLevelItem($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除级别条件
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteLevelItem(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteLevelItemDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminLevelItemFacade::multipleDeleteLevelItem($requestDTO, $adminObject);
        }

        return $result;
    }
}
