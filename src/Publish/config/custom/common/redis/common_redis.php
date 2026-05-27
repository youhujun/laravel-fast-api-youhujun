<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-23 17:50:42
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-14 20:27:43
 * @FilePath: \youhu-laravel-api-12\config\custom\common\redis\common_redis.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 *
 * 使用示例:
 *
 *  $redisAdminTokenKey = config('common_redis.admin_token.key');
    $redisAdminKey = config('common_redis.admin.key');
    $redisAdminInfoKey = config('common_redis.admin_info.key');
    $redisAdminRolesKey = config('common_redis.admin_roles.key');

    $redisAdminField = config('common_redis.admin.field');
    $redisAdminInfoField = config('common_redis.admin_info.field');
    $redisAdminRolesField = config('common_redis.admin_roles.field');

    $redisUserTokenKey = config('common_redis.user_token.key');
    $redisUserKey = config('common_redis.user.key');
    $redisUserInfoKey = config('common_redis.user_info.key');
    $redisUserRolesKey = config('common_redis.user_roles.key');

    $redisUserField = config('common_redis.user.field');
    $redisUserInfoField = config('common_redis.user_info.field');
    $redisUserRolesField = config('common_redis.user_roles.field');
 */
return [
    //redis存储时间
    'ttl' => [
        'login' => 12 * 60 * 60,
    ],
    'job' => [
        'block_number' => env('JOB_BLOCK_NUMBER', 0),
        'allow_number' => env('JOB_ALLOW_NUMBER', 5),
        'every_number' => env('JOB_EVERY_NUMBER', 2),
    ],
    //系统配置初始化缓存
    'system_config' => ['key' => 'system:config','field' => 'systen_config_init_done'],
    'system_region' => ['key' => 'system:config','field' => 'system_region'],
    //系统默认头像
    'system_avatar_url' => ['key' => 'system:system_config','field' => 'system_avatar_url'],
    //用户头像
    'user_avatar_url' => ['key' => 'user:user_avatar','field' => 'user_avatar_url-'],
    //管理员对象
    'admin' => ['key' => 'admin:admin','field' => 'admin-'],
    //管理员token
    'admin_token' => ['key' => 'admin_token:','field' => 'admin_tokne-'],
    //管理员信息
    'admin_info' => ['key' => 'admin_info:admin_info','field' => 'admin_info-'],
    //管理员角色
    'admin_roles' => ['key' => 'admin_roles:admin_roles','field' => 'admin_roles-'],
    //用户对象
    'user' => ['key' => 'user:user','field' => 'user-'],
    //用户token
    'user_token' => ['key' => 'user_token:','field' => 'user_tokne-'],
    //用户信息
    'user_info' => ['key' => 'user_info:user_info','field' => 'user_info-'],
    //用户级别
    'user_level' => ['key' => 'user_level:user_level','field' => 'user_level-'],
    //用户角色
    'user_roles' => ['key' => 'user_roles:user_roles','field' => 'user_roles-'],
    //系统默认头像
    'default_avatar_uid' => ['key' => 'default_avatar_uid:default_avatar_uid','field' => 'default_avatar_uid-'],
    //系统默认相册封面
    'default_cover_uid' => ['key' => 'default_cover_uid:default_cover_uid','field' => 'default_cover_uid-'],
    //系统默认相册
    'system_default_album_uid' => ['key' => 'system_default_album_uid:system_default_album_uid','field' => 'system_default_album_uid-'],
    //管理员默认相册
    'admin_default_album_uid' => ['key' => 'admin_default_album_uid:admin_default_album_uid','field' => 'admin_default_album_uid-'],
    //用户默认相册
    'user_default_album_uid' => ['key' => 'user_default_album_uid:user_default_album_uid','field' => 'user_default_album_uid-'],
    //用户微信openid
    'user_openid' => ['key' => 'user_openid:user_openid','field' => 'user_openid-'],
    //用户微信unionid
    'user_unionid' => ['key' => 'user_unionid:user_unionid','field' => 'user_unionid-'],

];
