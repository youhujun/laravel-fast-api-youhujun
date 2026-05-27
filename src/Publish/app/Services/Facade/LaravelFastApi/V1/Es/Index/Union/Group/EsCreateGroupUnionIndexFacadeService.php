<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 02:41:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 11:09:47
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\Union\Group;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade
 */
class EsCreateGroupUnionIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateGroupUnionIndexFacadeService test";
    }

    /**
     * 创建用户父级关联索引
     */
    public function createRolePermissionUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.role_permission_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建role_permission_unions索引', 'info');
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
                        // 基础字段
                        'id' => ['type' => 'keyword'],
                        'permission_id' => ['type' => 'keyword'],
                        'role_id' => ['type' => 'keyword'],
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
                    $this->consoleOutput('创建role_permission_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createRolePermissionUnionsIndexEror','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createRolePermissionUnionsIndexEror');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建role_permission_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有role_permission_unions索引', 'info');
            }
        }
    }

    /**
     * 创建用户父级关联索引
     */
    public function createUserRoleUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.user_role_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_role_unions索引', 'info');
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
                        // 基础字段
                        'user_role_union_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'role_id' => ['type' => 'keyword'],
                        'type' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建user_role_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createRolePermissionUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createRolePermissionUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_role_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_role_unions索引', 'info');
            }
        }
    }

    /**
     * 创建用户父级关联索引
     */
    public function createUserSourceUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.user_source_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_source_unions索引', 'info');
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
                        // 基础字段
                        'user_source_union_uid' => ['type' => 'keyword'],  // 商品分类关联记录雪花ID（主键）
                        'shard_key' => ['type' => 'keyword'],               // 分片锚点
                        'shard_db' => ['type' => 'keyword'],                // 分片库
                        'shard_table' => ['type' => 'keyword'],             // 分片表
                        // 业务字段
                        'user_uid' => ['type' => 'keyword'],     // 用户uid（分片锚点）
                        'first_uid' => ['type' => 'keyword'],    // 一级uid
                        'second_uid' => ['type' => 'keyword'],  // 二级uid
                        'sort' => ['type' => 'keyword'],  // 排序
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
                    $this->consoleOutput('创建user_source_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserSourceUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createUserSourceUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_source_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_source_unions索引', 'info');
            }
        }
    }

    /**
     * 创建商品分类关联索引
     */
    public function createGoodsClassUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.goods_class_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建goods_class_unions索引', 'info');
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
                        // 基础字段
                        'goods_class_union_uid' => ['type' => 'keyword'],  // 商品分类关联记录雪花ID（主键）
                        'shard_key' => ['type' => 'keyword'],               // 分片锚点
                        'shard_db' => ['type' => 'keyword'],                // 分片库
                        'shard_table' => ['type' => 'keyword'],             // 分片表
                        // 业务字段
                        'goods_uid' => ['type' => 'keyword'],               // 商品雪花ID（分片锚点）
                        'goods_class_id' => ['type' => 'keyword'],           // 分类id
                        'goods_class_one_depp_id' => ['type' => 'keyword'],  // 一级分类id
                        'goods_class_two_depp_id' => ['type' => 'keyword'], // 二级分类id
                        'goods_class_three_depp_id' => ['type' => 'keyword'], // 三级分类id
                        'goods_class_four_depp_id' => ['type' => 'keyword'], // 四级分类id
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
                    $this->consoleOutput('创建goods_class_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createGoodsClassUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createGoodsClassUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建goods_class_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有goods_class_unions索引', 'info');
            }
        }
    }

	/**
	 * 用户和微信uninonid关联
	 */
	public function createUserWechatUnionidIndex(): void
	{
		$indexName = config('common_es.indices.union.user_wechat_unionids');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_wechat_unionids索引', 'info');
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
                        // 基础字段
                        'user_wechat_unionid_uid' => ['type' => 'keyword'],  
                        'shard_key' => ['type' => 'keyword'],               
                        'shard_db' => ['type' => 'keyword'],                
                        'shard_table' => ['type' => 'keyword'],           
                        'user_uid' => ['type' => 'keyword'],               
                        'unionid' => ['type' => 'keyword'],           
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
                    $this->consoleOutput('创建user_wechat_unionids索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserWechatUnionidIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createUserWechatUnionidIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_wechat_unionids索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_wechat_unionids索引', 'info');
            }
        }
	}

    /**
     * 创建用户抖音配置关联索引
     */
    public function createUserSystemDouYinConfigUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.user_system_douyin_config_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_system_douyin_config_unions索引', 'info');
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
                        // 基础字段
                        'user_system_douyin_config_union_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'system_douyin_config_id' => ['type' => 'keyword'],
                        'openid' => ['type' => 'keyword','ignore_above' => 128],
                        'session_key' => ['type' => 'keyword','ignore_above' => 256],
                        'verified_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'verified_time' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建user_system_douyin_config_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserSystemDouYinConfigUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createUserSystemDouYinConfigUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_system_douyin_config_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_system_douyin_config_unions索引', 'info');
            }
        }
    }

    /**
     * 创建用户微信配置关联索引
     */
    public function createUserSystemWechatConfigUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.user_system_wechat_config_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_system_wechat_config_unions索引', 'info');
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
                        // 基础字段
                        'user_system_douyin_config_union_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'system_wechat_config_id' => ['type' => 'keyword'],
                        'openid' => ['type' => 'keyword','ignore_above' => 128],
                        'session_key' => ['type' => 'keyword','ignore_above' => 256],
                        'type' => ['type' => 'integer'],
                        'access_token' => ['type' => 'keyword','ignore_above' => 256],
                        'refresh_token' => ['type' => 'keyword','ignore_above' => 256],
                        'scope' => ['type' => 'keyword'],
                        'expires_in' => ['type' => 'integer'],
                        'is_snapshotuser' => ['type' => 'integer'],
                        'verified_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'verified_time' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建user_system_wechat_config_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserSystemWechatConfigUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createUserSystemWechatConfigUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_system_wechat_config_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_system_wechat_config_unions索引', 'info');
            }
        }
    }

    /**
     * 创建文章标签关联索引
     */
    public function createGoodsLabelUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.goods_label_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建goods_label_unions索引', 'info');
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
                        // 基础字段
                        'goods_label_union_uid' => ['type' => 'keyword'],  // 商品标签关联记录雪花ID（主键）
                        'shard_key' => ['type' => 'keyword'],               // 分片锚点
                        'shard_db' => ['type' => 'keyword'],                // 分片库
                        'shard_table' => ['type' => 'keyword'],             // 分片表
                        // 业务字段
                        'goods_uid' => ['type' => 'keyword'],               // 商品雪花ID（分片锚点）
                        'label_id' => ['type' => 'keyword'],                  // 标签ID
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
                    $this->consoleOutput('创建goods_label_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createGoodsLabelUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createGoodsLabelUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建goods_label_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有goods_label_unions索引', 'info');
            }
        }
    }

    /**
     * 创建文章分类关联索引
     */
    public function createArticleCategoryUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.article_category_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建article_category_unions索引', 'info');
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
                        // 基础字段
                        'article_category_union_uid' => ['type' => 'keyword'],  // 文章分类关联记录雪花ID（主键）
                        'shard_key' => ['type' => 'keyword'],               // 分片锚点
                        'shard_db' => ['type' => 'keyword'],                // 分片库
                        'shard_table' => ['type' => 'keyword'],             // 分片表
                        // 业务字段
                        'article_uid' => ['type' => 'keyword'],               // 文章雪花ID（分片锚点）
                        'category_id' => ['type' => 'keyword'],           // 分类id
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
                    $this->consoleOutput('创建article_category_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createArticleCategoryUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createArticleCategoryUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建article_category_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有article_category_unions索引', 'info');
            }
        }
    }

    /**
     * 创建文章标签关联索引
     */
    public function createArticleLabelUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.article_label_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建article_label_unions索引', 'info');
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
                        // 基础字段
                        'article_label_union_uid' => ['type' => 'keyword'],  // 文章标签关联记录雪花ID（主键）
                        'shard_key' => ['type' => 'keyword'],               // 分片锚点
                        'shard_db' => ['type' => 'keyword'],                // 分片库
                        'shard_table' => ['type' => 'keyword'],             // 分片表
                        // 业务字段
                        'article_uid' => ['type' => 'keyword'],               // 文章雪花ID（分片锚点）
                        'label_id' => ['type' => 'keyword'],                  // 标签ID
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
                    $this->consoleOutput('创建article_label_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createArticleLabelUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createArticleLabelUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建article_label_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有article_label_unions索引', 'info');
            }
        }
    }

    /**
     * 创建用户级别配置项关联索引
     */
    public function createUserlevelItemlUnionsIndex(): void
    {
        $indexName = config('common_es.indices.union.user_level_item_unions');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_level_item_unions索引', 'info');
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
                        // 基础字段
                        'id' => ['type' => 'keyword'],
                        // 业务字段
                        'user_level_id' => ['type' => 'keyword'],
                        'level_item_id' => ['type' => 'keyword'],
                        'value_type' => ['type' => 'integer'],
                        'value' => ['type' => 'long'],
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
                    $this->consoleOutput('创建user_level_item_unions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createArticleLabelUnionsIndexError','result' => $result], 'EsCreateGroupUnionIndexFacadeService', 'createArticleLabelUnionsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_level_item_unions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_level_item_unions索引', 'info');
            }
        }
    }
}
