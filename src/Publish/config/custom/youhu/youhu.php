<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-27 04:02:33
 * @FilePath: \youhu-laravel-api-12\config\custom\youhu\youhu.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

return [
    //是否发布
    'publish' => env('YOUHU_PUBLISH', false),
    //是否运行
    'runing' => env('YOUHU_RUNING', false),
    //是否是微服务
    'is_ms' => env('YOUHU_MS', false),
    //数据库连接
    'db_connection' => env('DB_CONNECTION_YOUHU', 'mysql'),
	    //雪花id机器配置
    'snowflake_machine_id' => env('YOUHU_SNOWFLAKE_MACHINE_ID', 1),

    // 分片配置（和ShardFacade的setConfig规则完全对齐）
    'shard' => [
        'db_count' => env('YOUHU_SHARD_DB_COUNT', 1),       // 分库数（工具包默认1）
        'table_count' => env('YOUHU_SHARD_TABLE_COUNT', 1), // 分表数（工具包默认1）
        'db_prefix' => env('YOUHU_SHARD_DB_PREFIX', 'ds_'), // 库前缀（工具包默认ds_）
        'default_db' => env('YOUHU_SHARD_DEFAULT_DB', 'ds_0'), // 默认库
    ],
];
