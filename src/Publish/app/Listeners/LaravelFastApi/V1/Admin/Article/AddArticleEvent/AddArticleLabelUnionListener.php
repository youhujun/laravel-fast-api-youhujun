<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-10 13:12:38
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 21:07:25
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleLabelUnionListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\Article\Union\ArticleLabelUnion;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent
 */
class AddArticleLabelUnionListener
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

        $label_id_array =get_cascader_array($requestDTO->label_cascader_id_array) ;

        $insertDataArray = [];

        if (count($label_id_array)) {
            foreach ($label_id_array as $label_id) {
                $insertDataArray[] = [
                    'article_label_union_uid' => get_snow_flake_id(),
                    'article_uid' => $articleObject->biz_id,
                    'label_id' => $label_id,
                ];
            }

            $articleLabelUnionResult = ShardHelperFacade::insertBatchWithShard(ArticleLabelUnion::class, $insertDataArray, 'article_uid');

            if (!isset($articleCategoryUnionResult) || !$articleCategoryUnionResult) {
                if ($isTransation) {
                    DB::rollBack();
                }

                throw new CommonException('AddArticleLabelError');
            }

             $indexName = config('common_es.indices.union.article_label_unions');

            $startTime = microtime(true);
            $total = 0;

            ArticleLabelUnion::queryByShard($articleObject->biz_id)
            ->select(['*'])
            ->cursor()
            ->chunk(config('common.chunk_size.es_sync'))
            ->each(function ($chunk) use (&$total, $indexName) {
                $articleLabelUnionCollection = $chunk;

                $esDataArray = $articleLabelUnionCollection->map(function ($articleLabelUnionObject) {
                    $configKey = get_shard_config_key();

                    return [
                        '_docId' => $articleLabelUnionObject->article_label_union_uid,
                        'shard_key' => $articleLabelUnionObject->shard_key,
                        'shard_db' => ShardFacade::getDbName($articleLabelUnionObject->article_uid, $configKey),
                        'shard_table' => ShardFacade::getTableName($articleLabelUnionObject->article_uid, 'article_label_unions', $configKey),
                        'article_label_union_uid' => $articleLabelUnionObject->article_label_union_uid,
                        'article_uid' => $articleLabelUnionObject->article_uid,
                        'label_id' => $articleLabelUnionObject->label_id,
                        'created_time' => $articleLabelUnionObject->created_time,
                        'updated_time' => $articleLabelUnionObject->updated_time,
                        'created_at' => $articleLabelUnionObject->created_at,
                        'updated_at' => $articleLabelUnionObject->updated_at,
                        'deleted_at' => $articleLabelUnionObject->deleted_at,
                    ];
                })->toArray();

                $result = EsFacade::batchActDoc($indexName, $esDataArray);

                if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                    plog(['error' => "es批量同步article_label_unions数据失败",'$result' => $result], 'EsAddArticleCategoryUnionJob', 'handleError');
                }
                // 统计处理总数
                $total += count($esDataArray);
            });

            $endTime = microtime(true);
            $costTime = round($endTime - $startTime, 2);

            plog(['info' => "批量同步article_category_unions数据完成",'total' => $total,'costTime' => $costTime], 'EsAddArticleLabelUnionJob', 'handleError');
            }
    }
}
