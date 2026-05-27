<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-23 13:48:26
 * @LastEditors: youhujun youhu8888@163.com & xueer & codebuddy
 * @LastEditTime: 2026-03-29 20:52:18
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\User\UserTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Test\V1\User;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Facades\Common\V1\User\User\CommonUserFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\Api\Auth\YouHuAuthService;
use App\Facades\LaravelFastApi\V1\Es\Sync\EsSyncYouhuAuthServiceFacade;
use App\Facades\LaravelFastApi\V1\Es\Index\EsCreateUserIndexFacade;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;

/**
 * @see \App\Facades\Test\V1\User\UserTestFacade
 */
class UserTestFacadeService
{
    public function test()
    {
        echo "UserTestFacadeService test";

        //$userCollection = User::queryByAllShard()->select(['user_uid', 'account_name'])->get();

        $userCollection = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->select(['user_uid', 'account_name']);
            },
            'account_name',
            ['develop', 'super', 'admin', 'user']
        );

        p($userCollection);
        //EsCreateUserIndexFacade::test();
        //$this->testCreateUserIndex();
        //\App\Facades\LaravelFastApi\V1\Es\Sync\EsSyncAdminFacade::syncAdmin();
        //$this->testInsertBatchData();
        //$this->testGetUserInfo();
    }

    public function testGetUserInfo()
    {
        $userObject = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->where('account_status', 1)->where('account_name', 'develop');
            },
            'account_name',
            ['develop']
        )->first();


        $userInfoObject = UserInfo::queryByShard($userObject->biz_id)->where('user_uid', $userObject->biz_id)->first();

        p($userInfoObject);
    }

    //测试获取用户头像
    public function testGetUserAvatar(): void
    {
        $userObject = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->where('account_status', 1)->where('account_name', 'develop');
            },
            'account_name',
            ['develop']
        )->first();

        $userAvatarUrl = CommonUserFacade::getUserAvatar($userObject);

        p($userAvatarUrl);
    }

    //测试同步YouHuAuthService
    public function testSyncYouhuAuthService()
    {
        EsSyncYouhuAuthServiceFacade::syncYouhuAuthService();
    }

    public function testCreateUserIndex()
    {
        EsCreateUserIndexFacade::createUsersIndex();
    }

    public function testInsertBatchData()
    {
        $batchDataArray = [
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => 123, 'first_uid' => 1],
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => 456, 'first_uid' => 2],
        ];

        ShardHelperFacade::insertBatchWithShard(
            UserSourceUnion::class,
            $batchDataArray,
            'user_uid'
        );
    }
}
