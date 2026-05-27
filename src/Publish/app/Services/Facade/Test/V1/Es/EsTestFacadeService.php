<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 15:32:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-08 15:27:02
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\Es\EsTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Test\V1\Es;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Models\LaravelFastApi\V1\Admin\Admin;

/**
 * @see \App\Http\Controllers\TestController
 * @see \App\Facades\Test\V1\Es\EsTestFacade
 */
class EsTestFacadeService
{
    public function test()
    {
        //$this->testCreateIndex();
        // echo "EsTestFacadeService test";

        // $this->testEsSyncAdmin();
        //$this->testFindEsDoc();
        //$this->testEsSearch();
        //$this->testWhereInSearch();

        $indexName = config('common_es.indices.album.albums');

        $user_uid = '177391699987095';

        $esAlbumObject = EsQueryFacade::index($indexName)->whereNull('deleted_at')->where('album_type', 20)->where('is_default', 1)->where('user_uid', $user_uid)->get()->first();

        p($esAlbumObject);die;


    }

    public function __construct()
    {
    }

    public function testCreateIndex()
    {
        //p('创建索引');
        \App\Facades\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacade::test();
        \App\Facades\LaravelFastApi\V1\Es\Index\Union\Group\EsCreateGroupUnionIndexFacade::test();
    }

    /**
     * 测试ES同步管理员数据
     */
    public function testEsSyncAdmin()
    {
        $adminObject = Admin::queryByAllShard()->where('account_name', 'super')->first();

        $indexName = config('common_es.indices.admins');

        p($adminObject);

        $updateDataArray = [
            'remember_token' => $adminObject->remember_token,
            'updated_at' => date('Y-m-d H:i:s', time())
        ];

        $result = EsFacade::updateDoc($indexName, $adminObject->biz_id, $updateDataArray);

        p($result);
    }

    public function testFindEsDoc()
    {
        $indexName = config('common_es.indices.admins');

        $adminObject = Admin::queryByAllShard()->where('account_name', 'super')->first();


        $result = EsFacade::findDoc($indexName, $adminObject->biz_id);

        p($result);

        /**
         * Array
            (
                [code] => 0
                [msg] => es文档查找成功
                [status] => 1
                [error] => Array
                    (
                        [_index] => youhu_admins_index
                        [_type] => _doc
                        [_id] => 276412040935018
                        [_version] => 14
                        [_seq_no] => 25
                        [_primary_term] => 1
                        [found] => 1
                        [_source] => Array
                            (
                                [shard_db] => ds_0
                                [shard_table] => admins_0
                                [admin_uid] => 276412040935018
                                [user_uid] => 276407645251371
                                [phone] =>
                                [account_name] => super
                                [account_status] => 1
                                [created_at] => 2026-02-12 18:18:21
                                [updated_at] => 2026-03-26 16:57:08
                                [deleted_at] =>
                                [id_number] =>
                                [nick_name] => superAdmin
                                [real_name] =>
                                [solar_birthday_at] =>
                                [chinese_birthday_at] =>
                                [sex] => 10
                                [introduction] => I am a super administrator
                                [ablum_uid] => 276413739674426
                                [avatar] => https://visit.youhujun.com/api.youhujun.com/config/avatar/01-avatar-system.png
                                [remember_token] => X7iii6hCjR1Jqyr2i68mVvpsfWYk8WdSwCK2HzMyq6p0TJeSUIlNq80KIxVO
                            )

                    )

                [data] => Array
                    (
                        [shard_db] => ds_0
                        [shard_table] => admins_0
                        [admin_uid] => 276412040935018
                        [user_uid] => 276407645251371
                        [phone] =>
                        [account_name] => super
                        [account_status] => 1
                        [created_at] => 2026-02-12 18:18:21
                        [updated_at] => 2026-03-26 16:57:08
                        [deleted_at] =>
                        [id_number] =>
                        [nick_name] => superAdmin
                        [real_name] =>
                        [solar_birthday_at] =>
                        [chinese_birthday_at] =>
                        [sex] => 10
                        [introduction] => I am a super administrator
                        [ablum_uid] => 276413739674426
                        [avatar] => https://visit.youhujun.com/api.youhujun.com/config/avatar/01-avatar-system.png
                        [remember_token] => X7iii6hCjR1Jqyr2i68mVvpsfWYk8WdSwCK2HzMyq6p0TJeSUIlNq80KIxVO
                    )

            )
         */
    }

    public function testEsSearch()
    {
        $indexName = config('common_es.indices.admins');

        $adminObject = Admin::queryByAllShard()->where('account_name', 'super')->first();

        $queryArray = [
            'match' => ['remember_token' => $adminObject->remember_token]
        ];

        $result = EsFacade::searchDoc($indexName, $queryArray);

        //p($result);

        if (isset($result) && $result['code'] == 0) {
            $adminArray = $result['data']['hits']['hits'][0]['_source'];
            //p($adminArray);
            $adminModelArray = [
                'admin_uid' => $adminArray['admin_uid'],
                'user_uid' => $adminArray['user_uid'],
                'remember_token' => $adminArray['remember_token'],
                'account_name' => $adminArray['account_name'],
                'phone' => $adminArray['phone'],
                'account_status' => $adminArray['account_status'],
            ];

            $adminModelObject = new Admin();
            $adminModelObject->fill($adminModelArray);

            p($adminModelObject);

            //$adminModdel = new Admin($adminModelArray);
        }
    }


    public function testWhereInSearch()
    {
        $user_uid_array = [261071290206934,262212111510254];

        $indexName = config('common_es.indices.user.users');

        $queryArray = [
            'bool' => [
                'must' => [
                    // whereIn user_uid
                    [
                        'terms' => [
                            'user_uid' => $user_uid_array
                        ]
                    ]
                ],
                // 自动过滤已软删除的数据（deleted_at IS NULL）
                'must_not' => [
                    [
                        'exists' => [
                            'field' => 'deleted_at'
                        ]
                    ]
                ]
            ]
        ];

        $esResult = EsFacade::searchDoc($indexName, $queryArray);

        //p($esResult);

        $total = 0;

        $dataArray = [];
        $dataPreArray = [];

        if (isset($esResult['data']['hits']['total']['value'])) {
            $total = $esResult['data']['hits']['total']['value'];
        }

        if ($total) {
            $dataPreArray = $esResult['data']['hits']['hits'];
        }

        foreach ($dataPreArray as $dataPre) {
            $dataArray[] = $dataPre['_source'];
        }
        /**
         * Array
            (
                [code] => 0
                [msg] => es文档批量查询
                [status] => 1
                [error] =>
                [data] => Array
                    (
                        [took] => 2
                        [timed_out] =>
                        [_shards] => Array
                            (
                                [total] => 1
                                [successful] => 1
                                [skipped] => 0
                                [failed] => 0
                            )

                        [hits] => Array
                            (
                                [total] => Array
                                    (
                                        [value] => 2
                                        [relation] => eq
                                    )

                                [max_score] => 1
                                [hits] => Array
                                    (
                                        [0] => Array
                                            (
                                                [_index] => youhu_users_index
                                                [_type] => _doc
                                                [_id] => 262212111510254
                                                [_score] => 1
                                                [_source] => Array
                                                    (
                                                        [_docId] => 262212111510254
                                                        [shard_db] => ds_0
                                                        [shard_table] => users_0
                                                        [user_uid] => 262212111510254
                                                        [phone] => 15688523143
                                                        [email] =>
                                                        [account_name] => 21d910ca
                                                        [account_status] => 0
                                                        [invite_code] => 0026
                                                        [real_auth_status] => 10
                                                        [level_id] => 1
                                                        [created_at] => 2026-03-29 17:21:56
                                                        [updated_at] => 2026-03-29 17:21:56
                                                        [deleted_at] =>
                                                        [id_number] =>
                                                        [nick_name] =>
                                                        [real_name] =>
                                                        [solar_birthday_at] =>
                                                        [chinese_birthday_at] =>
                                                        [sex] => 0
                                                        [introduction] =>
                                                        [ablum_uid] => 262213730505164
                                                        [avatar] =>
                                                    )

                                            )

                                        [1] => Array
                                            (
                                                [_index] => youhu_users_index
                                                [_type] => _doc
                                                [_id] => 261071290206934
                                                [_score] => 1
                                                [_source] => Array
                                                    (
                                                        [_docId] => 261071290206934
                                                        [shard_db] => ds_0
                                                        [shard_table] => users_0
                                                        [user_uid] => 261071290206934
                                                        [phone] => 15688523142
                                                        [email] =>
                                                        [account_name] => 03748568
                                                        [account_status] => 0
                                                        [invite_code] => 0025
                                                        [real_auth_status] => 10
                                                        [level_id] => 1
                                                        [created_at] => 2026-03-29 17:17:24
                                                        [updated_at] => 2026-03-29 17:17:24
                                                        [deleted_at] =>
                                                        [id_number] =>
                                                        [nick_name] =>
                                                        [real_name] =>
                                                        [solar_birthday_at] =>
                                                        [chinese_birthday_at] =>
                                                        [sex] => 0
                                                        [introduction] =>
                                                        [ablum_uid] => 261072938560295
                                                        [avatar] =>
                                                    )

                                            )

                                    )

                            )

                    )

            )
         */
    }
}
