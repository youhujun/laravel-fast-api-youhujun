<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 16:34:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 13:48:35
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\Log\EsCreateApiLogIndexFacadeService.php
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
 *@see \App\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateApiLogIndexFacade
 */
class EsCreateApiLogIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateApiLogIndexFacadeService test";
    }


    /**
     * 创建API事件日志索引
     */
    public function createApiEventLogsIndex(): void
    {
        $indexName = config('common_es.indices.logs.api_event_logs');

        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建admins索引', 'info');
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
                        // 核心业务字段
                        'user_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        //冷热数据分离 1热 0冷
                        'data_type' => ['type' => 'integer'],
                        // 服务和链路追踪字段
                        //所属服务编码（youhu-base/youhu/youhushop）
                        'service_code' => ['type' => 'keyword'],
                        //请求链路ID（全链路追踪）
                        'request_id' => ['type' => 'keyword'],
                        // 操作人字段
                        //操作人类型：10-系统 2-管理员 30-用户 40-第三方
                        'operator_type' => ['type' => 'integer'],
                        //操作人UID（用户/管理员UID，系统则为0）
                        'operator_uid' => ['type' => 'keyword'],

                        // 事件相关字段
                        'event_type' => ['type' => 'integer'],
                        'event_route_action' => ['type' => 'keyword'],
                        'event_name' => ['type' => 'text', 'analyzer' => 'ik_max_word', 'search_analyzer' => 'ik_smart'],
                        'event_code' => ['type' => 'keyword'],
                        'note' => ['type' => 'text', 'analyzer' => 'ik_max_word', 'search_analyzer' => 'ik_smart'],

                        // 状态字段 事件状态：0-待处理 10-成功 20-失败 30-重试中
                        'evnet_status' => ['type' => 'integer'],

                        // 扩展字段（JSON）
                        'ext_json' => ['type' => 'object', 'enabled' => true],
                        // 时间字段
 						'created_time'=>['type' => 'integer'],
						'updated_time'=>['type' => 'integer'],
						'created_at' => ['type' => 'date', 'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'created_time' => ['type' => 'long'],
                        'updated_at' => ['type' => 'date', 'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_time' => ['type' => 'long'],
                        'deleted_at' => ['type' => 'date', 'format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建api_event_logs索引成功', 'info');
                }
            } else {
                plog(['error' => 'createApiEventLogsIndexError','result' => $result], 'EsCreateApiEventLogIndexFacadeService', 'createApiEventLogsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建api_event_logs索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有admins索引', 'info');
            }
        }
    }
}
