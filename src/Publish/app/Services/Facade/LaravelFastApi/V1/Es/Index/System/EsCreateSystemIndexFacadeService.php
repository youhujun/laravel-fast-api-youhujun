<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 03:33:13
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-15 00:26:49
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\System;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade
 */
class EsCreateSystemIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateSystemConfigIndexFacadeService test";
    }


    /**
     * 创建系统配置表索引
     */
    public function createSystemConfigIndex(): void
    {
        $indexName = config('common_es.indices.system.system_configs');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建system_configs索引', 'info');
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
                        'item_type' => ['type' => 'integer'],
                        'item_label' => ['type' => 'keyword','ignore_above' => 256],
                        'item_value' => ['type' => 'keyword','ignore_above' => 512],
                        'item_price' => ['type' => 'integer'],
                        'item_path' => ['type' => 'keyword','ignore_above' => 512],
                        'item_introduction' => ['type' => 'keyword','ignore_above' => 512],
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
                    $this->consoleOutput('创建system_configs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemConfigIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createSystemConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建system_configs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有system_configs索引', 'info');
            }
        }
    }

    /**
     * 创建菜单索引
     */
    public function createPermissionIndex(): void
    {
        $indexName = config('common_es.indices.system.permissions');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建permissions索引', 'info');
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
                        // 菜单类型 10菜单MENU,20目录CATALOG,30外链EXTLINK,40按钮BUTTON
                        'type' => ['type' => 'integer'],
                        // 前端路由相关
                        'route_name' => ['type' => 'keyword', 'ignore_above' => 64],
                        'route_path' => ['type' => 'keyword', 'ignore_above' => 64],
                        'component' => ['type' => 'keyword', 'ignore_above' => 128],
                        'hidden' => ['type' => 'integer'],
                        'always_show' => ['type' => 'integer'],
                        'redirect' => ['type' => 'keyword', 'ignore_above' => 64],
                        // 权限标识
                        'permission_tag' => ['type' => 'keyword', 'ignore_above' => 128],
                        // 菜单Meta信息
                        'meta_title' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 64]]],
                        'meta_icon' => ['type' => 'keyword', 'ignore_above' => 32],
                        'meta_no_cache' => ['type' => 'integer'],
                        'meta_affix' => ['type' => 'integer'],
                        'meta_breadcrumb' => ['type' => 'integer'],
                        'meta_active_menu' => ['type' => 'keyword', 'ignore_above' => 128],
                        // 状态和排序
                        'switch' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建permissions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createPermissionIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createPermissionIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建permissions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有permissions索引', 'info');
            }
        }
    }

    /**
     * 创建系统提示音配置索引
     */
    public function createSystemVoiceConfigIndex(): void
    {
        $indexName = config('common_es.indices.system.system_voice_configs');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建system_voice_configs索引', 'info');
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
                        'voice_title' => ['type' => 'keyword', 'ignore_above' => 128],
                        'channle_name' => ['type' => 'keyword', 'ignore_above' => 128],
                        'channle_event' => ['type' => 'keyword', 'ignore_above' => 128],
                        'voice_save_type' => ['type' => 'integer'],
                        'voice_url' => ['type' => 'keyword', 'ignore_above' => 128],
                        'voice_path' => ['type' => 'keyword', 'ignore_above' => 128],
                        'voice_file' => ['type' => 'keyword', 'ignore_above' => 128],
                        'note' => ['type' => 'keyword', 'ignore_above' => 128],
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
                    $this->consoleOutput('创建system_voice_configs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemVoiceConfigIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createSystemVoiceConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建system_voice_configs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有system_voice_configs索引', 'info');
            }
        }
    }

    /**
     * 创建系统微信配置表索引
     */
    public function createSystemWeChatConfigIndex(): void
    {
        $indexName = config('common_es.indices.system.system_wechat_configs');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建system_wechat_configs索引', 'info');
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
                        'name' => ['type' => 'keyword', 'ignore_above' => 64],
                        'type' => ['type' => 'integer'],
                        'appid' => ['type' => 'keyword', 'ignore_above' => 64],
                        'appsecret' => ['type' => 'keyword', 'ignore_above' => 64],
                        'note' => ['type' => 'keyword', 'ignore_above' => 128],
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
                    $this->consoleOutput('创建system_wechat_configs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemWeChatConfigIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createSystemWeChatConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建system_wechat_configs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有system_wechat_configs索引', 'info');
            }
        }
    }

    /**
     * 创建系统微信配置表索引
     */
    public function createSystemDouYinConfigIndex(): void
    {
        $indexName = config('common_es.indices.system.system_douyin_configs');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建system_douyin_configs索引', 'info');
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
                        'name' => ['type' => 'keyword', 'ignore_above' => 64],
                        'type' => ['type' => 'integer'],
                        'appid' => ['type' => 'keyword', 'ignore_above' => 64],
                        'appsecret' => ['type' => 'keyword', 'ignore_above' => 64],
                        'note' => ['type' => 'keyword', 'ignore_above' => 128],
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
                    $this->consoleOutput('创建system_douyin_configs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemDouYinConfigIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createSystemDouYinConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建system_douyin_configs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有system_douyin_configs索引', 'info');
            }
        }
    }
    /**
     * 创建系统角色表索引
     */
    public function createRoleIndex(): void
    {
        $indexName = config('common_es.indices.system.roles');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建roles索引', 'info');
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
                        'type' => ['type' => 'integer'],
                        'is_system' => ['type' => 'integer'],
                        'role_name' => ['type' => 'keyword','ignore_above' => 64],
                        'logic_name' => ['type' => 'keyword','ignore_above' => 128],
                        'switch' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建roles索引成功', 'info');
                }
            } else {
                plog(['error' => 'createSystemConfigIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createSystemConfigIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建roles索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有roles索引', 'info');
            }
        }
    }

    /**
     * 创建系统地区表索引
     */
    public function createRegionIndex(): void
    {
        $indexName = config('common_es.indices.system.regions');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建regions索引', 'info');
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
                        'region_name' => ['type' => 'keyword','ignore_above' => 128],
                        'region_area' => ['type' => 'keyword','ignore_above' => 128],
                        'latitude' => ['type' => 'keyword','ignore_above' => 32],
                        'longitude' => ['type' => 'keyword','ignore_above' => 32],
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
                    $this->consoleOutput('创建regions索引成功', 'info');
                }
            } else {
                plog(['error' => 'createRegionIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createRegionIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建regions索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有regions索引', 'info');
            }
        }
    }

    /**
     * 创建系统地区表索引
     */
    public function createBankIndex(): void
    {
        $indexName = config('common_es.indices.system.banks');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建banks索引', 'info');
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
                        'bank_name' => ['type' => 'keyword','ignore_above' => 128],
                        'bank_code' => ['type' => 'keyword','ignore_above' => 128],
                        'is_default' => ['type' => 'integer'],
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
                    $this->consoleOutput('创建banks索引成功', 'info');
                }
            } else {
                plog(['error' => 'createBankIndexError','result' => $result], 'EsCreateSystemConfigIndexFacadeService', 'createBankIndexError');
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建banks索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有banks索引', 'info');
            }
        }
    }
}
