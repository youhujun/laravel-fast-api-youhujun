<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 21:01:47
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleCategoryUnionListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Article\Union\ArticleCategoryUnion;

class UpdateArticleCategoryUnionListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $adminObject = $event->adminObject;
        $requestDTO = $event->requestDTO;
        $articleObject = $event->articleObject;
        $isTransation = $event->isTransation;

        $category_id_array = get_cascader_array($requestDTO->category_cascader_id_array);

        //先把原来的关联数据删除
        $deleteResult = ArticleCategoryUnion::queryByShard($articleObject->biz_id)->where('article_uid', $articleObject->biz_id)->forceDelete();

        //重新添加
        $insertDataArray = [];

        if (count($category_id_array)) {
            foreach ($category_id_array as $category_id) {
                $insertDataArray[] = [
                    'article_category_union_uid' => get_snow_flake_id(),
                    'article_uid' => $articleObject->biz_id,
                    'category_id' => $category_id,
                ];
            }

            $articleCategoryUnionResult = ShardHelperFacade::insertBatchWithShard(ArticleCategoryUnion::class, $insertDataArray, 'article_uid');

            if (!isset($articleCategoryUnionResult) || !$articleCategoryUnionResult) {
                if ($isTransation) {
                    DB::rollBack();
                }

                throw new CommonException('UpdateArticleCategoryError');
            }

            $indexName = config('common_es.indices.union.article_category_unions');

            //es也是先把原来的删除
            $deleteQueryArray = [
                'match' => [
                    'article_uid' => $articleObject->biz_id,
                ]
            ];

            $esDeleteResult = EsFacade::batchDeleteDoc($indexName, $deleteQueryArray);

            if (!isset($esDeleteResult['code']) || $esDeleteResult['code'] != 0) {
                plog(['error' => 'es删除文章分类关联失败','$articleObject' => $articleObject,'$esDeleteResult' => $esDeleteResult,'$adminObject' => $adminObject], 'EsUpdateArticleCategoryUnionJob', 'handleError');
                return;
            }

            // 开始同步文章分类关联数据
            $startTime = microtime(true);
            $total = 0;

            ArticleCategoryUnion::queryByShard($articleObject->biz_id)
            ->select(['*'])
            ->cursor()
            ->chunk(config('common.chunk_size.es_sync'))
            ->each(function ($chunk) use (&$total, $indexName) {
                $articleCategoryUnionCollection = $chunk;
                $esDataArray = $articleCategoryUnionCollection->map(function ($articleCategoryUnionObject) {
                    $configKey = get_shard_config_key();

                    return [
                        '_docId' => $articleCategoryUnionObject->article_category_union_uid,
                        'shard_key' => $articleCategoryUnionObject->shard_key,
                        'shard_db' => ShardFacade::getDbName($articleCategoryUnionObject->article_uid, $configKey),
                        'shard_table' => ShardFacade::getTableName($articleCategoryUnionObject->article_uid, 'article_category_unions', $configKey),
                        'article_category_union_uid' => $articleCategoryUnionObject->article_category_union_uid,
                        'article_uid' => $articleCategoryUnionObject->article_uid,
                        'category_id' => $articleCategoryUnionObject->category_id,
                        'created_time' => $articleCategoryUnionObject->created_time,
                        'updated_time' => $articleCategoryUnionObject->updated_time,
                        'created_at' => $articleCategoryUnionObject->created_at,
                        'updated_at' => $articleCategoryUnionObject->updated_at,
                        'deleted_at' => $articleCategoryUnionObject->deleted_at,
                    ];
                })->toArray();

                $result = EsFacade::batchActDoc($indexName, $esDataArray);

                if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                    plog(['error' => "es批量同步article_category_unions数据失败",'$result' => $result], 'EsUpdateArticleCategoryUnionJob', 'handleError');
                }
                // 统计处理总数
                $total += count($esDataArray);
            });

            $endTime = microtime(true);
            $costTime = round($endTime - $startTime, 2);

            plog(['info' => "批量同步article_category_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsUpdateArticleCategoryUnionJob', 'handleError');
            }
    }
}
