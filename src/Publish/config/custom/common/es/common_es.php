<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-17 04:24:24
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-23 04:35:29
 * @FilePath: \youhu-laravel-api-12\config\custom\common\es\common_es.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

return [
    'host' => env('ES_HOST', 'http://127.0.0.1:9200'),
    'user' => env('ES_USER'),
    'password' => env('ES_PASSWORD'),
    'setting' => [
        'shard_number' => env('ES_SHARD_NUMBER', 1),
        'replicas_number' => env('ES_REPLICAS_NUMBER', 0),
    ],
    'max_result_window' => env('ES_MAX_RESULT_WINDOW', 10000),
    'query_keyword_field_array' => [
        'phone', 'id_number', 'account_name','album_name','email', 'user_uid', 'admin_uid', 'biz_id', 'shard_key', 'shard_db', 'shard_table', 'status', 'real_auth_status', 'sex', 'user_type', 'is_real_auth', 'deleted_at', 'created_time', 'updated_time', 'created_at', 'updated_at'
    ],
    'indices' => [
        //系统
        'system' => [
            //系统配置
            'system_configs' => env('ES_PREFIX', 'yh_').'system_configs_index',
            //菜单
            'permissions' => env('ES_PREFIX', 'yh_').'permissions_index',
            //系统提示音配置
            'system_voice_configs' => env('ES_PREFIX', 'yh_').'system_voice_configs_index',
            //系统微信配置
            'system_wechat_configs' => env('ES_PREFIX', 'yh_').'system_wechat_configs_index',
            //系统抖音配置
            'system_douyin_configs' => env('ES_PREFIX', 'yh_').'system_douyin_configs_index',
            //系统角色
            'roles' => env('ES_PREFIX', 'yh_').'roles_index',
            //地区索引
            'regions' => env('ES_PREFIX', 'yh_').'regions_index',
            //银行索引
            'banks' => env('ES_PREFIX', 'yh_').'banks_index',

        ],
        //业务
        'business' => [
            //系统提现配置
            'system_withdraw_configs' => env('ES_PREFIX', 'yh_').'system_withdraw_configs_index',
            //商品分类
            'goods_classes' => env('ES_PREFIX', 'yh_').'goods_classes_index',
            //文章分类
            'article_categories' => env('ES_PREFIX', 'yh_').'article_categories_index',
            //标签
            'labels' => env('ES_PREFIX', 'yh_').'labels_index',
            //级别配置项
            'level_items' => env('ES_PREFIX', 'yh_').'level_items_index',
            //用户级别
            'user_levels' => env('ES_PREFIX', 'yh_').'user_levels_index',
            //手机轮播图
            'phone_banners' => env('ES_PREFIX', 'yh_').'phone_banners_index',
        ],
        //用户
        'user' => [
            //微服务授权
            'youhu_auth_services' => env('ES_PREFIX', 'yh_').'youhu_auth_services_index',
            //业务
            //管理员
            'admins' => env('ES_PREFIX', 'yh_').'admins_index',
            //用户
            'users' => env('ES_PREFIX', 'yh_').'users_index',
            //用户账户
            'user_amounts' => env('ES_PREFIX', 'yh_').'user_amounts_index',
            //用户地址
            'user_addresses' => env('ES_PREFIX', 'yh_').'user_addresses_index',
            //用户银行
            'user_banks' => env('ES_PREFIX', 'yh_').'user_banks_index',
            //用户身份证
            'user_id_cards' => env('ES_PREFIX', 'yh_').'user_id_cards_index',
            //用户图片索引
            'user_pictures' => env('ES_PREFIX', 'yh_').'user_pictures_index',
            //用户认证索引
            'user_certifications' => env('ES_PREFIX', 'yh_').'user_certifications_index',
            //用户联系人
            'user_phones' => env('ES_PREFIX', 'yh_').'user_phones_index',
        ],
        //相册
        'album' => [
            'albums' => env('ES_PREFIX', 'yh_').'albums_index',
            'album_pictures' => env('ES_PREFIX', 'yh_').'album_pictures_index',
        ],
        //文章
        'article' => [
            'articles' => env('ES_PREFIX', 'yh_').'articles_index',
        ],
        //日志
        'logs' => [
            //微服务
            'api_event_logs' => env('ES_PREFIX', 'yh_').'api_event_logs_index',
            //管理员事件日志
            'admin_event_logs' => env('ES_PREFIX', 'yh_').'admin_event_logs_index',
            //管理员登录日志
            'admin_login_logs' => env('ES_PREFIX', 'yh_').'admin_login_logs_index',
            //用户事件日志
            'user_event_logs' => env('ES_PREFIX', 'yh_').'user_event_logs_index',
            //用户登录日志
            'user_login_logs' => env('ES_PREFIX', 'yh_').'user_login_logs_index',
            //用户账户日志
            'user_amount_logs' => env('ES_PREFIX', 'yh_').'user_amount_logs_index',
            //用户系统币日志
            'user_coin_logs' => env('ES_PREFIX', 'yh_').'user_coin_logs_index',
            //用户积分
            'user_score_logs' => env('ES_PREFIX', 'yh_').'user_score_logs_index',
            //用户实名认证日志
            'user_real_auth_logs' => env('ES_PREFIX', 'yh_').'user_real_auth_logs_index',
            //用户位置日志
            'user_location_logs' => env('ES_PREFIX', 'yh_').'user_location_logs_index',
        ],
        //关联
        'union' => [
            //角色和权限的关联
            'role_permission_unions' => env('ES_PREFIX', 'yh_').'role_permission_unions_index',
            //用户和角色关联
            'user_role_unions' => env('ES_PREFIX', 'yh_').'user_role_unions_index',
            //用户父关联表
            'user_source_unions' => env('ES_PREFIX', 'yh_').'user_source_unions_index',
            //用户微信的unionid
            'user_wechat_unionids' => env('ES_PREFIX', 'yh_').'user_wechat_unionids_index',
            //用户和抖音系统配置关联
            'user_system_douyin_config_unions' => env('ES_PREFIX', 'yh_').'user_system_douyin_config_unions_index',
            //用户和微信系统配置关联
            'user_system_wechat_config_unions' => env('ES_PREFIX', 'yh_').'user_system_wechat_config_unions_index',
            //商品分类关联
            'goods_class_unions' => env('ES_PREFIX', 'yh_').'goods_class_unions_index',
            //文章分类关联
            'article_category_unions' => env('ES_PREFIX', 'yh_').'article_category_unions_index',
            //商品标签关联
            'goods_label_unions' => env('ES_PREFIX', 'yh_').'goods_label_unions_index',
            //文章标签关联
            'article_label_unions' => env('ES_PREFIX', 'yh_').'rticle_label_unions_index',
            //用户级别配置项关联
            'user_level_item_unions' => env('ES_PREFIX', 'yh_').'user_level_item_unions_index',
        ]

    ]

];
