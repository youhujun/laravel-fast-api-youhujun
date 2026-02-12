<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-12 19:11:29
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController.php
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
use App\Facades\LaravelFastApi\V1\Admin\Article\AdminArticleFacade;

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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'find' => ['bail','nullable',new CheckString()],
                    'timeRangePublish' => ['bail','nullable',new CheckArray()],
                    'timeRangeCreate' => ['bail','nullable',new CheckArray()],
                    'categoryArray' => ['bail','nullable',new CheckArray()],
                    'labelArray' => ['bail','nullable',new CheckArray()],
                    'is_top' => ['bail','nullable',new Numeric()],
                    'status' => ['bail','nullable',new Numeric()],
                    'sortType' => ['bail','nullable',new Numeric()],
                    'currentPage' => ['bail','nullable',new Numeric()],
                    'pageSize' => ['bail','nullable',new Numeric()]
                ],
                []
            );

            $validated = $validator->validated();

            //p($validated);die;

            $result = AdminArticleFacade::getArticle(f($validated), $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'type' => new Required(),
                    'title' => ['bail',new Required(),new CheckBetween(4, 35)],
                    'category_id' => ['bail',new Required(),new CheckArray()],
                    'categoryArray' => ['bail',new Required(),new CheckArray()],
                    'label_id' => ['bail','nullable',new CheckArray()],
                    'labelArray' => ['bail','nullable',new CheckArray()],
                    'is_top' => ['bail',new Numeric()],
                    'content' => ['bail',new Required(),new CheckString(),new CheckBetween(1, 6000)],
                    'published_time' => ['bail','nullable',new FormatTime(20)],
                    'sort' => ['bail',new Numeric()]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['title'])) {
                throw new RuleException('RuleRequiredError', 'title');
            }

            if (!isset($validated['category_id'])) {
                throw new RuleException('RuleRequiredError', 'category_id');
            }

            if (!isset($validated['categoryArray'])) {
                throw new RuleException('RuleRequiredError', 'categoryArray');
            }

            if (!isset($validated['content'])) {
                throw new RuleException('RuleRequiredError', 'content');
            }

            if (!isset($validated['type'])) {
                throw new RuleException('RuleRequiredError', 'type');
            }


            //p($validated);die;

            if (isset($validated['content'])) {
                $validated['content'] = htmlspecialchars($validated['content']);
            }

            $result = AdminArticleFacade::addArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validated = $request->validate(
                [
                    'id' => ['bail',new Required(),new Numeric()],
                    'type' => ['bail',new Required()],
                    'title' => ['bail',new Required(),new CheckBetween(4, 35),new CheckBetween(4, 35)],
                    'category_id' => ['bail',new Required(),new CheckArray()],
                    'categoryArray' => ['bail',new Required(),new CheckArray()],
                    'label_id' => ['bail','nullable',new CheckArray()],
                    'labelArray' => ['bail','nullable',new CheckArray()],
                    'is_top' => ['bail',new Numeric()],
                    'content' => ['bail',new Required(),new CheckString(),new CheckBetween(1, 6000)],
                    'published_time' => ['bail','nullable',new FormatTime(20)],
                    'sort' => ['bail',new Numeric()]
                ],
                []
            );

            if (isset($validated['content'])) {
                $validated['content'] = htmlspecialchars($validated['content']);
            }

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['type'])) {
                throw new RuleException('RuleRequiredError', 'type');
            }

            if (!isset($validated['title'])) {
                throw new RuleException('RuleRequiredError', 'title');
            }

            if (!isset($validated['category_id'])) {
                throw new RuleException('RuleRequiredError', 'category_id');
            }

            if (!isset($validated['categoryArray'])) {
                throw new RuleException('RuleRequiredError', 'categoryArray');
            }

            if (!isset($validated['content'])) {
                throw new RuleException('RuleRequiredError', 'content');
            }

            $result = AdminArticleFacade::updateArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail',new Required(),new Numeric()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            $result = AdminArticleFacade::toTopArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'selectId' => ['bail',new Required(),new CheckArray()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            $result = AdminArticleFacade::multipleToTopArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'selectId' => ['bail',new Required(),new CheckArray()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            $result = AdminArticleFacade::multipleUnTopArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => ['bail',new Required(),new Numeric()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            $result = AdminArticleFacade::deleteArticle($validated, $admin);
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
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'selectId' => ['bail',new Required(),new CheckArray()],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['selectId'])) {
                throw new RuleException('RuleRequiredError', 'selectId');
            }

            $result = AdminArticleFacade::multipleDeleteArticle($validated, $admin);
        }

        return $result;
    }
}
