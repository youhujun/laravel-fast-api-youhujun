<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 15:38:48
 * @FilePath: \youhu-laravel-api-12\config\custom\laravel-fast-api\youhujun.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

return [
    //组件包是发布运行在项目中还是 直接在组件包运行
    'publish' => env('YOUHUJUN_PUBLISH', false),
    //是否是开发模式
    'develop_mode' => env('YOUHUJUN_DEVELOP_MODE', false),
    //组件包项目是否运行
    'runing' => env('YOUHUJUN_RUNING', false),
    //是否是微服务
    'is_ms' => env('YOUHUJUN_MS', false),
    //数据库连接
    'db_connection' => env('DB_CONNECTION', 'ds_0'),
    //雪花id机器配置
    'snowflake_machine_id' => env('YOUHUJUN_SNOWFLAKE_MACHINE_ID', 1),

    // 分片配置（和ShardFacade的setConfig规则完全对齐）
    'shard' => [
        'db_count' => env('YOUHUJUN_SHARD_DB_COUNT', 1),       // 分库数（工具包默认1）
        'table_count' => env('YOUHUJUN_SHARD_TABLE_COUNT', 1), // 分表数（工具包默认1）
        'db_prefix' => env('YOUHUJUN_SHARD_DB_PREFIX', 'ds_'), // 库前缀（工具包默认ds_）
        'default_db' => env('YOUHUJUN_SHARD_DEFAULT_DB', 'ds_0'), // 默认库
    ],

];
