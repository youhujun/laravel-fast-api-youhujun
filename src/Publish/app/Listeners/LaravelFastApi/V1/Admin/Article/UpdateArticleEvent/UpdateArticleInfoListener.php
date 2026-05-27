<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-10 14:40:14
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-21 21:00:55
 * @FilePath: \youhu-laravel-api-12\app\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent\UpdateArticleInfoListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Article\UpdateArticleEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;
use App\Models\LaravelFastApi\V1\Article\ArticleInfo;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Events\Admin\Article\UpdateArticleEvent
 */
class UpdateArticleInfoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
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

        $articleInfoObject = ArticleInfo::queryByShard($articleObject->biz_id)->where('article_uid', $articleObject->biz_id)->first();

        if (!isset($articleInfoObject->biz_id)) {
            throw new CommonException('ThisDataNotExistsError');
        }

        $udpateDataArray = [
            'article_info' => $requestDTO->content,
        ];

        $updateResult = $articleInfoObject->updateWithShard($udpateDataArray);

        if (!$updateResult) {
            if ($isTransation) {
                DB::rollBack();
            }

            throw new CommonException('UpdateArticleInfoError');
        }

        $indexName = config('common_es.indices.article.articles');

        $configKey = get_shard_config_key();

        $updateDataArray = [
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
            'updated_at' => $articleObject->updated_at,
            'updated_time' => $articleObject->updated_time,
            'deleted_at' => $articleObject->deleted_at,
        ];

        $esResult = EsFacade::updateDoc($indexName, $articleObject->biz_id, $updateDataArray);

        if (!isset($esResult['code']) || $esResult['code'] != 0) {
            plog(['error' => 'es更新文章失败','$articleObject' => $articleObject,'$articleInfoObject' => $articleInfoObject,'$esResult' => $esResult,'$adminObject' => $adminObject], 'EsUpdateArticleJob', 'handleError');
        }
    }
}
