<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-02 12:27:01
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 13:47:30
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\Album;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacade
 */
class EsCreateAlbumIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateAlbumIndexFacadeService test";
    }

    /**
     * 创建相册索引
     */
    public function createAlbumsIndex(): void
    {
        $indexName = config('common_es.indices.album.albums');
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建albums索引', 'info');
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
                        'album_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'admin_uid' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'cover_album_picture_uid' => ['type' => 'keyword'],
                        'is_default' => ['type' => 'integer'],
                        'is_system' => ['type' => 'integer'],
                        'album_type' => ['type' => 'integer'],
                        'album_name' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'album_description' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建albums索引成功', 'info');
                }
            } else {
                plog(['error' => 'createAlbumsIndexError','result' => $result], 'EsCreateAlbumIndexFacadeService', 'createAlbumsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建albums索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有albums索引', 'info');
            }
        }
    }

    /**
     * 创建相册图片索引
     */
    public function createAlbumPicturesIndex(): void
    {
        $indexName = config('common_es.indices.album.album_pictures');
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建album_pictures索引', 'info');
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
                        'album_picture_uid' => ['type' => 'keyword'],
                        'album_uid' => ['type' => 'keyword'],
                        'admin_uid' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'picture_name' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'picture_tag' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'picture_path' => ['type' => 'keyword','ignore_above' => 512],
                        'picture_file' => ['type' => 'keyword'],
                        'picture_size' => ['type' => 'integer'],
                        'picture_spec' => ['type' => 'keyword'],
                        'picture_type' => ['type' => 'integer'],
                        'picture_url' => ['type' => 'keyword','ignore_above' => 512],
                        'picture' => ['type' => 'keyword','ignore_above' => 512],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建album_pictures索引成功', 'info');
                }
            } else {
                plog(['error' => 'createAlbumPicturesIndexError','result' => $result], 'EsCreateAlbumIndexFacadeService', 'createAlbumPicturesIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建album_pictures索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有album_pictures索引', 'info');
            }
        }
    }
}
