<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 02:44:53
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-19 00:40:13
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacadeService.php
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
 * @see \App\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacade
 */
class EsCreateUserIndexFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsCreateUserIndexFacadeService test";
    }


    /**
      * 创建users表的Elasticsearch索引
      *
      * 配置索引的分片、副本数量以及IK分词器，并定义用户相关字段的映射关系。
      * 包含用户基本信息（如UID、手机号、邮箱）、身份信息（如身份证、实名状态）、
      * 个人资料（如昵称、头像、简介）等字段的索引配置。
      *
      * @return void
      */
    public function createUsersIndex(): void
    {
        $indexName = config('common_es.indices.user.users');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建users索引', 'info');
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
						'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
						//user
                        'user_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
						'source_user_uid' => ['type' => 'keyword'],
						'parent_user_uid' => ['type' => 'keyword'],
						'account_status'=>['type' => 'integer'],
						'real_auth_status' => ['type' => 'integer'],
						'level_id' => ['type' => 'integer'],
						'source' => ['type' => 'integer'],
                        'remember_token' => ['type' => 'keyword','ignore_above' => 256],
                        'auth_token' => ['type' => 'keyword','ignore_above' => 256],
                        'account_name' => ['type' => 'keyword'],
                        'invite_code' => ['type' => 'keyword'],
						'phone_area_code'=>['type' => 'keyword'],
                        'phone' => ['type' => 'keyword'],
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
						//wechat
						'wecaht_official_openid' => ['type' => 'keyword'],
                        'wecaht_mini_openid' => ['type' => 'keyword'],
                        'wechat_unionid' => ['type' => 'keyword'],
                        //role
                        'role_name_json'=>['type' => 'keyword','ignore_above' => 512],
                        //cascader
                        'role_cascader_json' =>  ['type' => 'keyword','ignore_above' => 512],
                        //时间
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],

                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建users索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUsersIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUsersIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建users索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有users索引', 'info');
            }
        }
    }

    /**
     * 创建用户金额索引
     */
    public function createUserAmountsIndex(): void
    {
        $indexName = config('common_es.indices.user.user_amounts');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_amounts索引', 'info');
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
                        'user_amount_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'amount' => ['type' => 'integer'],
                        'bonus' => ['type' => 'integer'],
                        'prepare_bonus' => ['type' => 'integer'],
                        'coin' => ['type' => 'integer'],
                        'score' => ['type' => 'integer'],
                        'sort' => ['type' => 'integer'],
                        'note' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]
                        ],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_amounts索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserAmountsIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserAmountsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_amounts索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_amounts索引', 'info');
            }
        }
    }

    /**
     * 创建用户地址索引
     */
    public function createUserAddressIndex(): void
    {
        $indexName = config('common_es.indices.user.user_addresses');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_addresses索引', 'info');
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
                        'user_address_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'address_type' => ['type' => 'integer'],
                        'is_default' => ['type' => 'integer'],
                        'is_top' => ['type' => 'integer'],
                        'phone' => ['type' => 'keyword'],
                        'address_info' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]
                        ],
                        'country_id' => ['type' => 'integer'],
                        'province_id' => ['type' => 'integer'],
                        'region_id' => ['type' => 'integer'],
                        'city_id' => ['type' => 'integer'],
                        'towns_id' => ['type' => 'integer'],
                        'village_id' => ['type' => 'integer'],
                        'country_name' => ['type' => 'keyword'],
                        'province_name' => ['type' => 'keyword'],
                        'country_name' => ['type' => 'keyword'],
                        'region_name' => ['type' => 'keyword'],
                        'city_name' => ['type' => 'keyword'],
                        'towns_name' => ['type' => 'keyword'],
                        'village_name' => ['type' => 'keyword'],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_addresses索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserAmountsIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserAmountsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_addresses索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_addresses索引', 'info');
            }
        }
    }

    /**
     * 创建用户身份证索引
     */
    public function createUserIdCardsIndex(): void
    {
        $indexName = config('common_es.indices.user.user_id_cards');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_id_cards索引', 'info');
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
                        'user_card_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'id_card_front_uid' => ['type' => 'keyword'],
                        'id_card_back_uid' => ['type' => 'keyword'],
                        'id_card_front_picture' => ['type' => 'keyword'],
                        'id_card_back_picture' => ['type' => 'keyword'],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_id_cards索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserAmountsIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserAmountsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_id_cards索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_id_cards索引', 'info');
            }
        }
    }

    /**
    * 创建用户身份证索引
    */
    public function createUserBanksIndex(): void
    {
        $indexName = config('common_es.indices.user.user_banks');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_banks索引', 'info');
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
                        'user_bank_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'bank_id' => ['type' => 'integer'],
                        'bank_name' => ['type' => 'keyword'],
                        'bank_front_uid' => ['type' => 'keyword'],
                        'bank_back_uid' => ['type' => 'keyword'],
                        'bank_front_picture' => ['type' => 'keyword'],
                        'bank_back_picture' => ['type' => 'keyword'],
                        'bank_number' => ['type' => 'keyword'],
                        'bank_account' => ['type' => 'keyword'],
                        'bank_address' => ['type' => 'keyword'],
                        'is_default' => ['type' => 'integer'],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_banks索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserAmountsIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserAmountsIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_banks索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_banks索引', 'info');
            }
        }
    }

    /**
    * 创建用户图片表索引
    */
    public function createUserPicturesIndex(): void
    {
        $indexName = config('common_es.indices.user.user_pictures');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_pictures索引', 'info');
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
                        'user_picture_uid' => ['type' => 'keyword'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'keyword'],
                        'album_picture_uid' => ['type' => 'integer'],
                        'is_default' => ['type' => 'integer'],
                        'type' => ['type' => 'integer'],
                        'data_type' => ['type' => 'integer'],
                        'picture' => ['type' => 'keyword'],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_pictures索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserPicturesIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserPicturesIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_pictures索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_pictures索引', 'info');
            }
        }
    }

    /**
    * 创建用认证索引
    */
    public function createUserCertificationsIndex(): void
    {
        $indexName = config('common_es.indices.user.user_certifications');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_certifications索引', 'info');
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
                        'user_certification_uid' => ['type' => 'long'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'long'],
                        'cert_type' => ['type' => 'integer'],
                        'cert_status' => ['type' => 'integer'],
                        'certified_time' => ['type' => 'integer'],
                        'certified_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'auditor_uid' => ['type' => 'integer'],
                        'cert_remark' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]]
                        ],
                        'expired_time' => ['type' => 'integer'],
                        'expired_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_certifications索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserPicturesIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserPicturesIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_certifications索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_certifications索引', 'info');
            }
        }
    }

    /**
    * 创建用认证索引
    */
    public function createUserPhoneIndex(): void
    {
        $indexName = config('common_es.indices.user.user_phones');

        //判断是否存在
        $isExist = EsFacade::indexExists($indexName);

        if (!$isExist) {
            if (app()->runningInConsole()) {
                $this->consoleOutput('开始创建user_phones索引', 'info');
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
                        'user_phone_uid' => ['type' => 'long'],
                        'shard_key' => ['type' => 'keyword'],
                        'shard_db' => ['type' => 'keyword'],
                        'shard_table' => ['type' => 'keyword'],
                        'user_uid' => ['type' => 'long'],
                        'type' => ['type' => 'integer'],
                        'is_default' => ['type' => 'integer'],
                        'phone' => ['type' => 'keyword'],
                        'sort' => ['type' => 'integer'],
                        'created_time' => ['type' => 'integer'],
                        'updated_time' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'updated_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                        'deleted_at' => ['type' => 'date','format' => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis||strict_date_optional_time"],
                    ]
                ]
            ]);

            if (isset($result['code']) && $result['code'] == 0) {
                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_phones索引成功', 'info');
                }
            } else {
                plog(['error' => 'createUserPhoneIndexError','result' => $result], 'EsCreateUserIndexFacadeService', 'createUserPhoneIndexError');

                if (app()->runningInConsole()) {
                    $this->consoleOutput('创建user_phones索引失败', 'error');
                }
            }
        } else {
            if (app()->runningInConsole()) {
                $this->consoleOutput('已有user_phones索引', 'info');
            }
        }
    }
}
