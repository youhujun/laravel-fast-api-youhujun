<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-20 22:20:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 15:52:52
 * @FilePath: \youhu-laravel-api-12\config\custom\common\common.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

return [
    'default_password' => env('DEFAULT_PASSWORD', 'abc321'),
    //aes加解密配置
    'aes' => [
        //key
        'key' => env('AES_KEY', 'youhujun19880703'),
        //向量
        'iv' => env('AES_IV', '0123456789ABEDEF')
    ],
    //分片大小
    'chunk_size' => [
        'default' => env('CHUNK_SIZE', 1000),
        'es_sync' => env('CHUNK_SIZE_ES_SYNC', 1000),
        'data_export' => env('CHUNK_SIZE_EXPORT', 5000),
    ],
    #是否开启系统配置缓存
    'is_cache_system_config' => env('IS_CACEH_SYSTEM_CONFIG', false),
    //是否开启熔断
    'is_self_protected' => env('IS_SELF_PROTECTED', true),
    //是否是开发模式
    'is_develop_mode' => env('IS_DEVELOP_MODE', true),
    //微信公众号是否是开发模式
    'wechat_official_develop_mode' => env('WECHAT_OFFICIAL_DEVELOP_MODE', false),

    //日志调试
    'debug' => [
        'verify_sign_log' => true,
        'send_appid_log' => true,
    ],


];
