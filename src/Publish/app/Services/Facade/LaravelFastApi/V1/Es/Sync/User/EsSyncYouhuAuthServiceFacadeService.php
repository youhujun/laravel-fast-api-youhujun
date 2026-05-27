<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 14:49:43
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 01:26:41
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Sync\User\EsSyncYouhuAuthServiceFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Sync\User;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Facade\Common\V1\Es\Console\Traits\EsFacadeServiceBaseTrait;
use YouHuJun\Tool\App\Facades\V1\Utils\Shard\ShardFacade;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Models\LaravelFastApi\V1\Api\Auth\YouHuAuthService;

/**
 * @see \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncYouhuAuthServiceFacade
 */
class EsSyncYouhuAuthServiceFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncYouhuAuthServiceFacadeService test";
    }

    public function __construct()
    {
    }

    /**
    * 执行数据同步
    */
    public function syncYouhuAuthService(): void
    {
        if (app()->runningInConsole()) {
            $this->consoleOutput('开始批量执行所有youhu_auth_services数据同步ES--2', 'info');
        }

        $startTime = microtime(true);
        $total = 0;
        $indexName = config('common_es.indices.user.youhu_auth_services');

        YouHuAuthService::queryByAllShard()
        ->select(['user_uid','shard_key','secret_key','auth_token','service_flag','status','created_at', 'updated_at','deleted_at'])
        ->cursor()
        ->chunk(config('common.chunk_size.es_sync'))
        ->each(function ($chunk) use (&$total, $indexName) {
            $youhuAuthServiceCollection = $chunk;

            $esDataArray = $youhuAuthServiceCollection->map(function ($youhuAuthServiceObject) {
                $configKey = get_shard_config_key();
                return [
                    '_docId' => $youhuAuthServiceObject->user_uid,
                    'shard_key' => $youhuAuthServiceObject->shard_key,
                    'shard_db' => ShardFacade::getDbName($youhuAuthServiceObject->user_uid, $configKey),
                    'shard_table' => ShardFacade::getTableName($youhuAuthServiceObject->user_uid, 'youhu_auth_services', $configKey),
                    'user_uid' => $youhuAuthServiceObject->user_uid,
                    'secret_key' => $youhuAuthServiceObject->secret_key,
                    'auth_token' => $youhuAuthServiceObject->auth_token,
                    'service_flag' => $youhuAuthServiceObject->service_flag,
                    'status' => $youhuAuthServiceObject->status,
                    'created_time' => $youhuAuthServiceObject->created_time,
                    'updated_time' => $youhuAuthServiceObject->updated_time,
                    'created_at' => $youhuAuthServiceObject->created_at,
                    'updated_at' => $youhuAuthServiceObject->updated_at,
                    'deleted_at' => $youhuAuthServiceObject->deleted_at,
                ];
            })->toArray();

            $result = EsFacade::batchActDoc($indexName, $esDataArray);

            if (!isset($result) || !isset($result['code']) || $result['code'] != 0) {
                plog(['error' => 'es批量同步用户认证数据失败','$result' => $result], 'EsSyncYouhuAuthServiceFacadeService', 'syncYouhuAuthServiceError');
            }
            // 统计处理总数
            $total += count($esDataArray);
        });

        $endTime = microtime(true);
        $costTime = round($endTime - $startTime, 2);

        plog(['info' => '批量同步用户数据完成','total' => $total,'costTime' => $costTime], 'EsSyncYouhuAuthServiceFacadeService', 'syncYouhuAuthService');

        if (app()->runningInConsole()) {
            $this->consoleOutput('批量执行所有youhu_auth_services数据同步ES结束--2', 'info');
        }
    }
}
