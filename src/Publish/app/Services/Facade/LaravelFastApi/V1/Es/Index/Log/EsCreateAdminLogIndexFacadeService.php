<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-31 22:34:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-25 23:55:15
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\Log;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacade
 */
class EsCreateAdminLogIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateAdminLogIndexFacadeService test";
    }

    //管理员登录日志
    public function createAdminEventLogsIndex(): void
    {
        $indexName = config('common_es.indices.logs.admin_event_logs');

        $isExist = EsFacade::indexExists($indexName);

        //判断是否存在
        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建admin_event_logs索引', 'info');
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
                        'admin_event_log_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'admin_uid' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'data_type' => ['type' => 'integer'],
                        'event_type' => ['type' => 'integer'],
                        'event_route_action' => ['type' => 'keyword'],
                        'event_name' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
                        'event_code' => ['type' => 'keyword'],
                        'note' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
						'created_time'=>['type' => 'integer'],
						'updated_time'=>['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admin_event_logs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createAdminEventLogsIndexError','result' => $result], 'EsCreateAdminLogIndexFacadeService', 'createAdminEventLogsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admin_event_logs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有admin_event_logs索引', 'info');
            }
        }
    }

    //管理员登录日志
    public function createAdminLoginLogsIndex(): void
    {
        $indexName = config('common_es.indices.logs.admin_login_logs');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建admin_login_logs索引', 'info');
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
                        'admin_login_log_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'admin_uid' => ['type' => 'keyword'],
                        'data_type' => ['type' => 'integer'],
                        'login_type' => ['type' => 'integer'],
                        'status' => ['type' => 'integer'],
                        'ip' => ['type' => 'keyword'],
                        'instruction' => [
                            'type' => 'text',
                            'analyzer' => 'ik_max_word',
                            'search_analyzer' => 'ik_smart'
                        ],
						'created_time'=>['type' => 'integer'],
						'updated_time'=>['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admin_login_logs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createAdminLoginLogsIndexError','result' => $result], 'EsCreateAdminLogIndexFacadeService', 'createAdminLoginLogsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admin_login_logs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有admin_login_logs索引', 'info');
            }
        }
    }
}
