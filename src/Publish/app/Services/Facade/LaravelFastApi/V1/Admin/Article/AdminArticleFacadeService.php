<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 23:18:49
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\Article;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\Admin\CommonException;
use App\Events\Admin\CommonEvent;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
//DTO
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\GetArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\AddArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\ToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleUnToTopArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\UpdateArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\DeleteArticleDTO;
use App\DTOs\LaravelFastApi\V1\Admin\Article\Article\MultipleDeleteArticleDTO;
//event
use App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent;
use App\Events\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent;
use App\Jobs\LaravelFastApi\V1\Admin\Article\PublishArticleJob;
use App\Models\LaravelFastApi\V1\Article\Article;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Article\EsArticleResource;
use App\Http\Resources\LaravelFastApi\V1\Es\Admin\Article\EsArticleCollection;

/**
 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController
 * @see \App\Facades\Admin\Article\AdminArticleFacade
 */
class AdminArticleFacadeService
{
    public function test()
    {
        echo "AdminArticleFacadeService test";
    }

    protected static $sortMapArray = [
        '4' => ['created_time','desc'],
        '3' => ['created_time','asc'],
        '2' => ['published_time','desc'],
        '1' => ['published_time','asc']
    ];

    /**
     * 获取文章
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function getArticle(GetArticleDTO $requestDTO, $adminObject)
    {
        $result = code(config('admin_code.GetArticleError'));

        $perPage = $requestDTO->pageSize;
        $currentPage = $requestDTO->currentPage;
        $label_cascader_id_array = $requestDTO->label_cascader_id_array;
        $category_cascader_id_array = $requestDTO->category_cascader_id_array;

        $category_id_array = get_cascader_array($category_cascader_id_array);
      
        $label_id_array = get_cascader_array($label_cascader_id_array);

        $indexName = config('common_es.indices.article.articles');

        $esArticleCategoryUnionIndexName = config('common_es.indices.union.article_category_unions');
        $esAticleLabelUnionIndexName = config('common_es.indices.union.article_label_unions');

        $category_article_uid_array = [];
        $label_article_uid_array = [];

        $max_size = config('common_es.max_result_window');

        if(count($category_id_array)){
            $category_article_uid_array = EsQueryFacade::index($esArticleCategoryUnionIndexName)
            ->whereIn('category_id', $category_id_array)
            ->limit($max_size)
            ->get()
            ->pluck('article_uid')
            ->toArray();
        }

        if(count($label_id_array)){
            $label_article_uid_array = EsQueryFacade::index($esAticleLabelUnionIndexName)
            ->whereIn('label_id', $label_id_array)
            ->limit($max_size)
            ->get()
            ->pluck('article_uid')
            ->toArray();
        }

        // 合并分类和标签筛选结果，获取文章UID数组
        $article_uid_array = array_unique(array_merge($category_article_uid_array, $label_article_uid_array));

        // 1. 初始化ES查询构造器
        $esQuery = EsQueryFacade::index($indexName);

        // 默认全局查询未删除用户
        $esQuery->whereNull('deleted_at');

        if(count($article_uid_array)){
            $esQuery->whereIn('article_uid', $article_uid_array);
        }

        //普通管理员查询自己的文章
        if (is_develop($adminObject) && !is_super($adminObject) && !is_article_admin($adminObject)) {
            $esQuery->where('admin_uid', $adminObject->admin_uid);
        }

        //置顶
        if (isset($requestDTO->is_top) && !empty($requestDTO->is_top)) {
            $esQuery->where('is_top', $requestDTO->is_top);
        }

        //发布状态
        if (isset($requestDTO->status) && !empty($requestDTO->status)) {
            $esQuery->where('status', $requestDTO->status);
        }

        //标题查找
        if (isset($requestDTO->find) && !empty($requestDTO->find) && $requestDTO->find) {
            $esQuery->whereLike('title', $requestDTO->find);
        }

        //发布时间
        if (isset($requestDTO->timeRangePublish) && \count($requestDTO->timeRangePublish)) {
            $startTime = strtotime($requestDTO->timeRangePublish[0]);
            $endTime = strtotime($requestDTO->timeRangePublish[1]);

            $esQuery->whereBetween('published_time', [$startTime, $endTime]);
        }

        //创建时间
        if (isset($requestDTO->timeRangeCreate) && \count($requestDTO->timeRangeCreate)) {
            $startTime = strtotime($requestDTO->timeRangeCreate[0]);
            $endTime = strtotime($requestDTO->timeRangeCreate[1]);

            $esQuery->whereBetween('created_time', [$startTime, $endTime]);
        }

        //排序
        if (isset($requestDTO->sortType)) {
            $sortType = $requestDTO->sortType;

            //$esQuery->orderBy(self::$sortMapArray[$sortType][0], self::$sortMapArray[$sortType][1]);
        }

        $articlePaginator = $esQuery->page($currentPage, $perPage)->paginate();

        // p($articlePaginator);
        // die;

        if (!optional($articlePaginator)) {
            throw new CommonException('GetArticleError');
        }

        $result = new EsArticleCollection($articlePaginator, ['code' => 0,'msg' => '获取文章列表成功']);

        return $result;
    }
    
    /**
     * 添加文章
     *
     * @param [type] $validated 表单验证完成的参数
     * @param [type] $adminObject 当前操作的用户
     * @return void
     */
    public function addArticle(AddArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.AddArticleError'));

        //判断发布时间
        $pre_published_time = $requestDTO->published_time;

        $published_time = 0;

        $published_at = date('Y-m-d H:i:s', time());

        if (!empty($pre_published_time)) {
            $published_time = strtotime($pre_published_time);
            $published_at = $pre_published_time;
        } else {
            $published_time = time();
            $published_at = date('Y-m-d H:i:s', time());
        }

        //如果发布时间 大于 现在时间 发布状态改为0 未发布
        //默认已发布
        $status = 10;
        if ($published_time > time()) {
            $status = 0;
        }

        $insertDataArray = [
            'article_uid' => get_snow_flake_id(),
            'admin_uid' => $adminObject->biz_id,
            'user_uid' => $adminObject->user_uid,
            'title' => $requestDTO->title,
            'is_top' => $requestDTO->is_top,
            'sort' => $requestDTO->sort,
            'type' => $requestDTO->type,
            'category_cascader_json' => json_encode($requestDTO->category_cascader_id_array),
            'label_cascader_json' => json_encode($requestDTO->label_cascader_id_array),
            'status' => $status,
            'published_at' => $published_at,
            'published_time' => $published_time,
        ];

        $articleObject =  ShardHelperFacade::createWithShard(Article::class, $adminObject->user_uid, $insertDataArray);

        if (!isset($articleObject->biz_id)) {
            throw new CommonException('AddArticleError');
        }

        AddArticleEvent::dispatch($adminObject, $articleObject, $requestDTO);

        CommonEvent::dispatch($adminObject, $requestDTO, 'AddArticle');

        PublishArticleJob::dispatchIf($articleObject->status === 0, $adminObject, $articleObject)->delay(now()->addSeconds($articleObject->published_time - time()));

        $result = code(['code' => 0,'msg' => '文章添加成功!']);

        return $result;
    }


    /**
     * 更新文章
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function updateArticle(UpdateArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.UpdateArticleError'));

        //判断发布时间
        $pre_published_time = $requestDTO->published_time;

        $published_time = 0;
        $published_at = date('Y-m-d H:i:s', time());

        if (!empty($pre_published_time)) {
            $published_time = strtotime($pre_published_time);
            $published_at = $pre_published_time;
        } else {
            $published_time = time();
            $published_at = date('Y-m-d H:i:s');
        }

        //如果发布时间 大于 现在时间 发布状态改为0 未发布
        //默认已发布
        $status = 10;
        if ($published_time > time()) {
            $status = 0;
        }

        $indexName = config('common_es.indices.article.articles');

        //先从es查询
        $esArticleObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('article_uid', $requestDTO->article_uid)->get()->first();

        //降级熔断
        if (!isset($esArticleObject->article_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $articleObject = Article::queryByShard($esArticleObject->user_uid)->where('article_uid', $requestDTO->article_uid)->first();

        if (!isset($articleObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'title' => $requestDTO->title,
            'is_top' => $requestDTO->is_top,
            'sort' => $requestDTO->sort,
            'type' => $requestDTO->type,
            'category_cascader_json' => json_encode($requestDTO->category_cascader_id_array),
            'label_cascader_json' => json_encode($requestDTO->label_cascader_id_array),
            'status' => $status,
            'published_at' => $published_at,
            'published_time' => $published_time,
        ];

        $udpateResult = $articleObject->updateWithShard($updateDataArray);

        if (!$udpateResult) {
            throw new CommonException('UpdateArticleError');
        }

        $articleObject = $articleObject->fresh();

        UpdateArticleEvent::dispatch($adminObject, $articleObject, $requestDTO);

        CommonEvent::dispatch($adminObject, $requestDTO, 'UpdateArticle');

        PublishArticleJob::dispatchIf($articleObject->status === 0, $adminObject, $articleObject)->delay(now()->addSeconds($articleObject->published_time - time()));

        $result = code(['code' => 0,'msg' => '文章更新成功!']);

        return $result;
    }


    /**
     * 置顶文章
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function toTopArticle(ToTopArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.TopArticleError'));

        $indexName = config('common_es.indices.article.articles');

        //先从es查询
        $esArticleObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('article_uid', $requestDTO->article_uid)->get()->first();

        //降级熔断
        if (!isset($esArticleObject->article_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $articleObject = Article::queryByShard($esArticleObject->user_uid)->where('article_uid', $requestDTO->article_uid)->first();

        if (!isset($articleObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'is_top' => $requestDTO->is_top ?? 1,

        ];

        $updateReuslt = $articleObject->updateWithShard($updateDataArray);

        if (!$updateReuslt) {
            throw new CommonException('TopArticleError');
        }

        $articleObject = $articleObject->fresh();

         $indexName = config('common_es.indices.article.articles');

        $updateDataArray = [
            'is_top' => $articleObject->is_top,
            'updated_at' => $articleObject->updated_at,
            'updated_time' => $articleObject->updated_time,
        ];

        $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es指定文章设置','$articleObject' => $articleObject,'$articleInfoObject' => $articleInfoObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsAddArticleJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'ToTopArticle');

        $result = code(['code' => 0,'msg' => '置顶文章成功!']);

        return $result;
    }

    /**
     * 批量置顶
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleToTopArticle(MultipleToTopArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleTopArticleError'));

        $indexName = config('common_es.indices.article.articles');

        $max_size = config('common_es.max_result_window');

        $updateDataArray = ['is_top' => $requestDTO->is_top ?? 1];

        $select_uid_array = $requestDTO->select_uid_array;

        $esArticleCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('article_uid', $select_uid_array)->limit($max_size)->get();

        foreach ($esArticleCollection as $esArticleObject) {
            $articleObject = Article::queryByShard($esArticleObject->user_uid)->where('article_uid', $esArticleObject->article_uid)->first();

            if (!isset($articleObject->biz_id)) {
                continue;
            }

            $updateReuslt = $articleObject->updateWithShard($updateDataArray);

            if (!$updateReuslt) {
                plog(['error' => '批量置顶文章失败','$articleObject' => $articleObject,'$updateDataArray' => $updateDataArray], 'AdminArticleFacadeService', 'multipleToTopArticle');
            }

            $indexName = config('common_es.indices.article.articles');

            $updateDataArray = [
                'is_top' => $articleObject->is_top,
                'updated_at' => $articleObject->updated_at,
                'updated_time' => $articleObject->updated_time,
            ];

            $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

            if (!isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es指定文章设置','$articleObject' => $articleObject,'$articleInfoObject' => $articleInfoObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsAddArticleJob', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleTopArticle');

        $result = code(['code' => 0,'msg' => '批量置顶文章成功!']);

        return $result;
    }



    /**
     * 删除文章
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function deleteArticle(DeleteArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.DeleteArticleError'));

        $indexName = config('common_es.indices.article.articles');

        //先从es查询
        $esArticleObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('article_uid', $requestDTO->article_uid)->get()->first();

        //降级熔断
        if (!isset($esArticleObject->article_uid)) {
            throw new CommonException('ServiceBusyError');
        }

        $articleObject = Article::queryByShard($esArticleObject->user_uid)->where('article_uid', $requestDTO->article_uid)->first();

        if (!isset($articleObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $updateReuslt = $articleObject->updateWithShard($updateDataArray);

        if (!$updateReuslt) {
            throw new CommonException('DeleteArticleError');
        }

        $indexName = config('common_es.indices.article.articles');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es删除文章失败','$articleObject' => $articleObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteArticleJob', 'handleError');
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'DeleteArticle');

        $result = code(['code' => 0,'msg' => '删除文章成功!']);

        return $result;
    }

    /**
     * 批量删除
     *
     * @param [type] $validated
     * @param [type] $adminObject
     * @return void
     */
    public function multipleDeleteArticle(MultipleDeleteArticleDTO $requestDTO, Admin $adminObject)
    {
        $result = code(config('admin_code.MultipleDeleteArticleError'));

        $indexName = config('common_es.indices.article.articles');

        $max_size = config('common_es.max_result_window');

        $updateDataArray = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];

        $select_uid_array = $requestDTO->select_uid_array;

        $esArticleCollection = EsQueryFacade::index($indexName)->whereNull('deleted_at')->whereIn('article_uid', $select_uid_array)->limit($max_size)->get();

        foreach ($esArticleCollection as $esArticleObject) {
            $articleObject = Article::queryByShard($esArticleObject->user_uid)->where('article_uid', $esArticleObject->article_uid)->first();

            if (!isset($articleObject->biz_id)) {
                continue;
            }

            $updateReuslt = $articleObject->updateWithShard($updateDataArray);

            if (!$updateReuslt) {
                plog(['error' => '批量删除文章失败','$articleObject' => $articleObject,'$updateDataArray' => $updateDataArray], 'AdminArticleFacadeService', 'multipleDeleteArticle');
            }

            $indexName = config('common_es.indices.article.articles');

            $updateDataArray = [
                'deleted_at' => date('Y-m-d H:i:s'),
            ];

            $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

            if (!isset($esResult['code']) || $esResult['code'] != 0) {
                plog(['error' => 'es删除文章失败','$articleObject' => $articleObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsDeleteArticleJob', 'handleError');
            }
        }

        CommonEvent::dispatch($adminObject, $requestDTO, 'MultipleDeleteArticle');

        $result = code(['code' => 0,'msg' => '批量删除文章成功!']);

        return $result;
    }
}
