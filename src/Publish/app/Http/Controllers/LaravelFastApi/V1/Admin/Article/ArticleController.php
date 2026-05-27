<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 20:40:38
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\GetArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\AddArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\UpdateArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\ToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleUnToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\DeleteArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleDeleteArticleDTO;
use App\Facades\LaravelFastApi\V1\Admin\Article\AdminArticleFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacadeService
 */
class ArticleController extends Controller
{
    /**
    * 获取文章
    *
    * @param Request $request
    * @return void
    */
    public function getArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new GetArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::getArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 添加文章
     *
     * @param Request $request
     * @return void
     */
    public function addArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new AddArticleDTO())->validate($request->all());

            //p($requestDTO);die;

            $result = AdminArticleFacade::addArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 修改文章
     *
     * @param Request $request
     * @return void
     */
    public function updateArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdateArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::updateArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 置顶文章
     *
     * @param Request $request
     * @return void
     */
    public function toTopArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new ToTopArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::toTopArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量置顶
     *
     * @param Request $request
     * @return void
     */
    public function multipleToTopArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleToTopArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::multipleToTopArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量取消置顶
     *
     * @param Request $request
     * @return void
     */
    public function multipleUnTopArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleUnToTopArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::multipleUnTopArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 删除文章
     *
     * @param Request $request
     * @return void
     */
    public function deleteArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new DeleteArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;
            $result = AdminArticleFacade::deleteArticle($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 批量删除
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteArticle(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new MultipleDeleteArticleDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            $result = AdminArticleFacade::multipleDeleteArticle($requestDTO, $adminObject);
        }

        return $result;
    }
}
