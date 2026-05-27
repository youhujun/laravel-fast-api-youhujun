<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-10 13:11:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 20:54:23
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent\AddArticleInfoListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Article\AddArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Models\LaravelFastApi\V1\Article\ArticleInfo;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\Article\AddArticleEvent
 */
class AddArticleInfoListener
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

        $articleInfoInsertDataArray = [
            'article_info_uid' => get_snow_flake_id(),
            'article_uid' => $articleObject->biz_id,
            'article_info' => $requestDTO->content,
        ];

        $articleInfoObject = ShardHelperFacade::createWithShard(ArticleInfo::class, $articleObject->biz_id, $articleInfoInsertDataArray);

        if (!isset($articleInfoObject->biz_id)) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('AddArticleInfoError');
        }

        $indexName = config('common_es.indices.article.articles');

        $configKey = get_shard_config_key();

        $inserDataArray = [
            '_docId' => $articleObject->biz_id,
            'article_uid' => $articleObject->biz_id,
            'shard_key' => $articleObject->shard_key,
            'shard_db' => ShardFacade::getDbName($articleObject->user_uid, $configKey),
            'shard_table' => ShardFacade::getTableName($articleObject->user_uid, 'articles', $configKey),
            'admin_uid' => $articleObject->admin_uid,
            'user_uid' => $articleObject->user_uid,
            'title' => $articleObject->title,
            'status' => $articleObject->status,
            'type' => $articleObject->type,
            'is_top' => $articleObject->is_top,
            'check_status' => $articleObject->check_status,
            'published_at' => $articleObject->published_at,
            'published_time' => $articleObject->published_time,
            'checked_at' => $articleObject->checked_at,
            'checked_time' => $articleObject->checked_time,
            'article_info' => $articleInfoObject->article_info,
            'sort' => $articleObject->sort,
            'category_cascader_json' => $articleObject->category_cascader_json,
            'label_cascader_json' => $articleObject->label_cascader_json,
            'created_at' => $articleObject->created_at,
            'created_time' => $articleObject->created_time,
            'updated_at' => $articleObject->updated_at,
            'updated_time' => $articleObject->updated_time,
            'deleted_at' => $articleObject->deleted_at,
        ];

        $esResult = EsFacade::createDoc($indexName, $inserDataArray, $articleObject->biz_id);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es添加文章失败','$articleObject' => $articleObject,'$articleInfoObject' => $articleInfoObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsAddArticleJob', 'handleError');
        }
    }
}
