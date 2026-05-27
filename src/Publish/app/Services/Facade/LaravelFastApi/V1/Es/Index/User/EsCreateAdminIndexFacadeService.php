<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 05:48:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 00:39:34
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\User\EsCreateAdminIndexFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\LaravelFastApi\V1\Es\Index\User;

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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateAdminIndexFacade
 */
class EsCreateAdminIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateAdminIndexFacadeService test";
    }

    /**
      * 创建admins表的Elasticsearch索引
      *
      * 配置索引的分片、副本数量以及IK分词器，并定义用户相关字段的映射关系。
      * 包含用户基本信息（如UID、手机号、邮箱）、身份信息（如身份证、实名状态）、
      * 个人资料（如昵称、头像、简介）等字段的索引配置。
      *
      * @return void
      */
    public function createAdminsIndex(): void
    {
        $indexName = config('common_es.indices.user.admins');

        //判断是否存在
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
						'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
						//admin
                        'admin_uid' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
						'account_status'=>['type' => 'integer'],
                        'remember_token' => ['type' => 'keyword','ignore_above' => 512],
                        'account_name' => ['type' => 'keyword','ignore_above' => 256],
						'phone_area_code'=>['type' => 'keyword'],
                        'phone' => ['type' => 'keyword','ignore_above' => 256],
						'password' => ['type' => 'keyword','ignore_above' => 128],
                        'email' => ['type' => 'keyword','ignore_above' => 256],
						//userinfo
                         'nick_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 128]]
                        ],
                        'real_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 128]]
                        ],
                        'id_number' => ['type' => 'keyword'],
                        'sex' => ['type' => 'integer'],
                        'solar_birthday_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
						'solar_birthday_time'=>['type' => 'integer'],
                        'chinese_birthday_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
						'chinese_birthday_time'=>['type' => 'integer'],
                        'introduction' => ['type' => 'keyword','ignore_above' => 512],
						//other
                        'avatar' => ['type' => 'keyword','ignore_above' => 512],
                        'ablum_uid' => ['type' => 'keyword'],
                        'qrcode' => ['type' => 'keyword','ignore_above' => 512],
                        //role
                        'role_name_json'=>['type' => 'keyword','ignore_above' => 512],
                         //cascader
                        'role_cascader_json' => ['type' => 'keyword','ignore_above' => 512],
                         //时间
						'created_time'=>['type' => 'integer'],
						'updated_time'=>['type' => 'integer'],
						'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admins索引成功', 'info');
                }
            } else {
                plog(['error' => 'createAdminsIndexError','result' => $result], 'EsCreateAdminIndexFacadeService', 'createAdminsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建admins索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有admins索引', 'info');
            }
        }
    }
}
