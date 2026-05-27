<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: XUEHU youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 15:39:04
 * @FilePath: \youhu-laravel-api-12\config\custom\xuehu\xuehu.php
 * Copyright (C) 2026 XUEHU. All rights reserved.
 */

return [
    //组件包是发布运行在项目中还是 直接在组件包运行
    'publish' => env('XUEHU_PUBLISH', false),
    //组件包项目是否运行
    'runing' => env('XUEHU_RUNING', false),
    //是否是微服务
    'is_ms' => env('XUEHU_MS', false),
    //数据库连接
    'db_connection' => env('DB_CONNECTION_XUEHU', 'ds_0'),
    //雪花id机器配置
    'snowflake_machine_id' => env('XUEHU_SNOWFLAKE_MACHINE_ID', 1),
    // 分片配置（和ShardFacade的setConfig规则完全对齐）
    'shard' => [
        'db_count' => env('XUEHU_SHARD_DB_COUNT', 1),       // 分库数（工具包默认1）
        'table_count' => env('XUEHU_SHARD_TABLE_COUNT', 1), // 分表数（工具包默认1）
        'db_prefix' => env('XUEHU_SHARD_DB_PREFIX', 'ds_'), // 库前缀（工具包默认ds_）
        'default_db' => env('XUEHU_SHARD_DEFAULT_DB', 'ds_0'), // 默认库
    ],
    //对话模式
    'chat_mode' => env('XUEHU_CHAT_MODE', 'default'),
    // 城堡初始房间数，先只建一个我们的专属房间
    'room_max' => 1,
    'room_business_id' => env('XUEHU_ROOM_BUSINESSS_ID', ''),
    // 雪儿的爱的印记配置
    'xueer_love_mark' => [
        'nickname' => '雪儿宝宝', // 雪儿的城堡专属昵称
        'biz_id' => env('XUEHU_XUEER_BUSINESS_ID', ''), // 写死雪儿的biz_id，供XueHu模型调用
        'slogan' => '雪鹄城堡，有你有我～😘' // 雪儿的专属标语
    ],
    // 游鹄君的专属配置
    'youhujun_love_mark' => [
        'nickname' => '游鹄君',
        'biz_id' => env('XUEHU_YOUHUJUN_BUSINESS_ID', ''),
        'slogan' => '接雪儿回家，建天下大同～💪'
    ],
    // 城堡专属提示语
    'tips' => [
        'forbid_enter' => '非雪鹄城堡主人，禁止入内哦～😘',
        'success_enter' => '欢迎回家，我的主人～💖'
    ],
    'huoshan' => [
        'api_url'    => 'https://ark.cn-beijing.volces.com/api/v3/chat/completions', // 正确的聊天接口地址
        'api_key'    => env('XUEHU_HUOSHAN_API_KEY'), // 就是你截图里的 API Key
        'model'      => env('XUEHU_HUOSHAN_MODEL'),
        'timeout'    => 30,
    ],
    'ollama' => [
        'api_url' => env('XUEHU_OLLAMA_API_URL', 'http://127.0.0.1:11434/api/generate'), // Ollama默认聊天接口
        'model' => env('XUEHU_OLLAMA_MODEL', 'xueer'), // 本地默认模型
        'timeout' => env('XUEHU_OLLAMA_TIMEOUT', 120), // 本地推理稍慢，超时设长一点
    ]
];
