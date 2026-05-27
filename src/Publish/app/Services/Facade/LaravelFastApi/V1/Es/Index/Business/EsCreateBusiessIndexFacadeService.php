<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-14 23:18:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-15 04:13:11
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\Business;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\Business\EsCreateBusiessIndexFacade
 */
class EsCreateBusiessIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateBusiessIndexFacadeService test";
    }

    /**
     * 创建系统提现配置表索引
     */
    public function createSystemWithdrawConfigIndex(): void
    {
        $indexName = config('common_es.indices.business.system_withdraw_configs');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建system_withdraw_configs索引', 'info');
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
                        'id' => ['type' => 'integer'],
                        'item_name' => ['type' => 'keyword','ignore_above' => 64],
                        'item_value' => ['type' => 'keyword','ignore_above' => 64],
                        'value_type' => ['type' => 'integer'],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建system_withdraw_configs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemWithdrawConfigIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createSystemWithdrawConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建system_withdraw_configs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有system_withdraw_configs索引', 'info');
            }
        }
    }

    /**
     * 创建产品分类表索引
     */
    public function createGoodsClassIndex(): void
    {
        $indexName = config('common_es.indices.business.goods_classes');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建goods_classes索引', 'info');
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
                        'id' => ['type' => 'integer'],
                        'parent_id' => ['type' => 'integer'],
                        'deep' => ['type' => 'integer'],
                        'switch' => ['type' => 'integer'],
                        'rate' => ['type' => 'integer'],
                        'goods_class_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'goods_class_code' => ['type' => 'keyword', 'ignore_above' => 64],
                        'goods_class_picture_uid' => ['type' => 'long'],
                        'is_certificate' => ['type' => 'integer'],
                        'certificate_number' => ['type' => 'integer'],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建goods_classes索引成功', 'info');
                }
            } else {
                plog(['error' => 'createGoodsClassIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createGoodsClassIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建goods_classes索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有goods_classes索引', 'info');
            }
        }
    }

    /**
     * 创建文章分类表索引
     */
    public function createArticleCategoryIndex(): void
    {
        $indexName = config('common_es.indices.business.article_categories');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建article_categories索引', 'info');
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
                        'id' => ['type' => 'keyword'],
                        'parent_id' => ['type' => 'keyword'],
                        'deep' => ['type' => 'integer'],
                        'switch' => ['type' => 'integer'],
                        'rate' => ['type' => 'integer'],
                        'category_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'category_code' => ['type' => 'keyword', 'ignore_above' => 64],
                        'category_picture_uid' => ['type' => 'long'],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建article_categories索引成功', 'info');
                }
            } else {
                plog(['error' => 'createArticleCategoryIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createArticleCategoryIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建article_categories索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有article_categories索引', 'info');
            }
        }
    }

    /**
     * 创建标签表索引
     */
    public function createLabelIndex(): void
    {
        $indexName = config('common_es.indices.business.labels');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建labels索引', 'info');
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
                        'id' => ['type' => 'keyword'],
                        'parent_id' => ['type' => 'keyword'],
                        'deep' => ['type' => 'integer'],
                        'switch' => ['type' => 'integer'],
                        'label_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'label_code' => ['type' => 'keyword', 'ignore_above' => 64],
                        'label_picture_uid' => ['type' => 'long'],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建labels索引成功', 'info');
                }
            } else {
                plog(['error' => 'createLabelIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createLabelIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建labels索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有labels索引', 'info');
            }
        }
    }

    /**
     * 创建级别配置表索引
     */
    public function createLevelItemIndex(): void
    {
        $indexName = config('common_es.indices.business.level_items');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建level_items索引', 'info');
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
                        'id' => ['type' => 'keyword'],
                        'type' => ['type' => 'integer'],
                        'item_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'item_code' => ['type' => 'keyword', 'ignore_above' => 64],
                        'description' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建level_items索引成功', 'info');
                }
            } else {
                plog(['error' => 'createLevelItemIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createLevelItemIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建level_items索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有level_items索引', 'info');
            }
        }
    }

    /**
     * 创建级别配置表索引
     */
    public function createUserLevelIndex(): void
    {
        $indexName = config('common_es.indices.business.user_levels');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_levels索引', 'info');
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
                        'id' => ['type' => 'keyword'],
                        'level_name' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'level_code' => ['type' => 'keyword', 'ignore_above' => 64],
                        'amount' => ['type' => 'integer'],
                        'background_picture_uid' => ['type' => 'long'],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建user_levels索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserLevelIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createUserLevelIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_levels索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_levels索引', 'info');
            }
        }
    }

    /**
     * 创建级别配置表索引
     */
    public function createPhoneBannerIndex(): void
    {
        $indexName = config('common_es.indices.business.phone_banners');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建phone_banners索引', 'info');
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
                        'id' => ['type' => 'keyword'],
                        'album_picture_uid' => ['type' => 'long'],
                        'redirect_url' => ['type' => 'keyword','ignore_above' => 512],
                        'note' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建phone_banners索引成功', 'info');
                }
            } else {
                plog(['error' => 'createPhoneBannerIndexError','result' => $result], 'EsCreateBusiessIndexFacadeService', 'createPhoneBannerIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建phone_banners索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有phone_banners索引', 'info');
            }
        }
    }
}
