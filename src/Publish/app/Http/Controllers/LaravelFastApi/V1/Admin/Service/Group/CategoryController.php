<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 01:55:24
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group\CategoryController.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Service\Group;

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
use App\Rules\Pub\CheckDbUnique;
use App\Rules\Pub\LetterNumberUnderLine;
use App\Rules\LaravelFastApi\V1\Admin\Common\TreeDeep;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\AddCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\UpdateCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\MoveCategoryDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Service\Group\Category\DeleteCategoryDTO;
use App\Facades\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacadeService
 */
class CategoryController extends Controller
{
    /**
    * 获取树形地址
    *
    * @return void
    */
    public function getTreeCategory()
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $result = AdminCategoryFacade::getTreeCategory();
        }

        return  $result;
    }

    /**
    * 添加顶级/下级文章分类
    *
    * @param Request $request
    * @return void
    */
    public function addCategory(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new AddCategoryDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminCategoryFacade::addCategory($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 修改文章分类
     *
     * @param Request $request
     * @return void
     */
    public function updateCategory(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        $id = check_id($request->input('id'));

        if (Gate::forUser($userObject)->allows('admin-role')) {

            $requestDTO = (new UpdateCategoryDTO($id))->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminCategoryFacade::updateCategory($requestDTO, $userObject);
        }
        return $result;
    }

    /**
     * 树形控件移动
     *
     * @param Request $request
     * @return void
     */
    public function moveCategory(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('super-role')) {
            $requestDTO = (new MoveCategoryDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminCategoryFacade::moveCategory($requestDTO, $userObject);
        }

        return $result;
    }

    /**
     * 删除文章分类
     *
     * @param Request $request
     * @return void
     */
    public function deleteCategory(Request $request)
    {
        $userObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($userObject)->allows('admin-role')) {
            $requestDTO = (new DeleteCategoryDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminCategoryFacade::deleteCategory($requestDTO, $userObject);
        }

        return $result;
    }
}
