<?php

return [
    //是否发布
    'publish' => env('YOUHUSHOP_PUBLISH', false),
    //是否运行
    'runing' => env('YOUHUSHOP_RUNING', false),
    //是否是微服务
    'is_ms' => env('YOUHUSHOP_MS', false),
    //数据库连接
    'db_connection' => env('DB_CONNECTION_YOUHUSHOP', 'mysql'),
    //雪花id机器配置
    'snowflake_machine_id' => env('YOUHUSHOP_SNOWFLAKE_MACHINE_ID', 1),
    // 分片配置（和ShardFacade的setConfig规则完全对齐）
    'shard' => [
        'db_count' => env('YOUHUSHOP_SHARD_DB_COUNT', 1),       // 分库数（工具包默认1）
        'table_count' => env('YOUHUSHOP_SHARD_TABLE_COUNT', 1), // 分表数（工具包默认1）
        'db_prefix' => env('YOUHUSHOP_SHARD_DB_PREFIX', 'ds_'), // 库前缀（工具包默认ds_）
        'default_db' => env('YOUHUSHOP_SHARD_DEFAULT_DB', 'ds_0'), // 默认库
    ],

];
