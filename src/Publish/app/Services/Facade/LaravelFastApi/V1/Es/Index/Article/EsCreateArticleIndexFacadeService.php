<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 02:38:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 21:13:56
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\Article;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacade
 */
class EsCreateArticleIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateArticleIndexFacadeService test";
    }

    /**
     * 创建文章索引
     */
    public function createArticlesIndex(): void
    {
        $indexName = config('common_es.indices.article.articles');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建articles索引', 'info');
            }

            $result = EsFacade::createIndex($indexName, [
                'settings' => [
                    // 单节点测试设1，生产可按数据量调
                    'number_of_shards' => config('common_es.setting.shard_number'),
                    // 测试环境不用副本，生产建议设1
                    'number_of_replicas' => config('common_es.setting.replicas_number'),
                    'analysis' => [
                        'analyzer' => [
                            // 全局默认细粒度分词
                            'default' => ['type' => 'ik_max_word'],
                            // 搜索时粗粒度分词
                            'default_search' => ['type' => 'ik_smart']
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'article_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'admin_uid' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        // 文章业务字段
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'status' => ['type' => 'integer'],              // 状态: 0未发布 10已发布
                        'type' => ['type' => 'integer'],               // 文章类型: 0无 10公告通知
                        'is_top' => ['type' => 'integer'],             // 是否置顶: 0不置顶 1置顶
                        'check_status' => ['type' => 'integer'],        // 审核状态: 0待审核 10审核中 20审核通过 30审核不通过
                        'published_at' => ['type' => 'date', 'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'published_time' => ['type' => 'integer'],
                        'checked_at' => ['type' => 'date', 'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'checked_time' => ['type' => 'integer'],
                        'article_info' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'sort' => ['type' => 'integer'],
                        'categor_cascader_json' => ['type' => 'keyword','ignore_above' => 512],
                        'label_cascader_json' => ['type' => 'keyword','ignore_above' => 512],
                        'created_time' => ['type' => 'integer',],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建articles索引成功', 'info');
                }
            } else {
                plog(['error' => 'createArticlesIndexError','result' => $result], 'EsCreateArticleIndexFacadeService', 'createArticlesIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建articles索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有articles索引', 'info');
            }
        }
    }
}
