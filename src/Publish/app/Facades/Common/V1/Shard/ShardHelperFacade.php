<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-14 02:18:47
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 15:34:56
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Shard\ShardHelperFacade.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Facades\Common\V1\Shard;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Collection;

/**
 * @method static array getShardInfo(string $modelClass, string|int $uid) 获取分片完整信息
 * @method static string getTableName(string $modelClass, string|int $uid) 获取分表名
 * @method static string getDbName(string|int $uid) 获取分库名
 * @method static mixed withShardContext(string $modelClass, string|int $uid, callable $callback) 带分片上下文执行闭包
 * @method static mixed createWithShard(string $modelClass, string|int $uid, array $data) 带分片创建模型
 * @method static Collection queryAllShards(string $modelClass, callable $callback, string $targetField = '', array $targets = []) 遍历所有分表查询

 * @see \App\Services\Facade\Common\V1\Shard\ShardHelperFacadeService
 */


class ShardHelperFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "ShardHelperFacade";
    }

    /**
     * 【可选】添加静态提示方法（增强IDE提示，和你通用工具写法一致）
     */
    public static function getShardInfo(string $modelClass, string|int $uid): array
    {
        return static::getFacadeRoot()->getShardInfo($modelClass, $uid);
    }

    public static function getTableName(string $modelClass, string|int $uid): string
    {
        return static::getFacadeRoot()->getTableName($modelClass, $uid);
    }

    public static function getDbName(string|int $uid): string
    {
        return static::getFacadeRoot()->getDbName($uid);
    }

    public static function withShardContext(string $modelClass, string|int $uid, callable $callback): mixed
    {
        return static::getFacadeRoot()->withShardContext($modelClass, $uid, $callback);
    }

    public static function createWithShard(string $modelClass, string|int $uid, array $data): mixed
    {
        return static::getFacadeRoot()->createWithShard($modelClass, $uid, $data);
    }

    public static function queryAllShards(string $modelClass, callable $callback, string $targetField = '', array $targets = []): Collection
    {
        return static::getFacadeRoot()->queryAllShards($modelClass, $callback, $targetField, $targets);
    }

    public static function insertBatchWithShard(string $modelClass, array $batchData, string $shardKeyField = 'user_uid'): mixed
    {
        return static::getFacadeRoot()->insertBatchWithShard($modelClass, $batchData, $shardKeyField);
    }
}
