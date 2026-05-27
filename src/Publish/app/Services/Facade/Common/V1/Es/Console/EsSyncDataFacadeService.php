<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 01:11:15
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 02:39:13
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Es\Console\EsSyncDataFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\Es\Console;

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

/**
 * @see \App\Facades\Common\V1\Es\Console\EsSyncDataFacade
 */
class EsSyncDataFacadeService
{
    use EsFacadeServiceBaseTrait;
    public function test()
    {
        echo "EsSyncDataFacadeService test";
    }

    //索引映射
    private $indexMap;

    public function __construct()
    {
        $this->indexMap = [
            'all' => [
                'code' => 0,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有数据同步ES--1', 'info');
                    //系统设置
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemConfig();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncPermission();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemVoiceCOnfig();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRole();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRegion();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncBank();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemWechatConfig();
                    //业务设置
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncCategory();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncLevelItem();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncUserLevel();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncWithdrawConfig();

                    //用户
                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade::syncUser();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade::syncUserAomunt();

                    //管理员
                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacade::syncAdmin();

                    //youhuauth
                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncYouhuAuthServiceFacade::syncYouhuAuthService();

                    //相册
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbums();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbumPictures();

                    //union
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncRolePermissionUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserLevelItemUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserRoleUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserSourceUnions();

                    $this->consoleOutput('批量执行所有数据同步ES结束--1', 'info');
                }
            ],
            'system_config' => [
                'code' => 10,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有system_config数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemConfig();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncPermission();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemVoiceCOnfig();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRole();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncRegion();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncBank();

                    $this->consoleOutput('批量执行所有system_config数据同步ES结束--1', 'info');
                }
            ],
            'business' => [
                'code' => 20,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有business数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncCategory();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncLevelItem();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncUserLevel();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Business\EsSyncBusinessFacade::syncWithdrawConfig();

                    $this->consoleOutput('批量执行所有business数据同步ES结束--1', 'info');
                }
            ],
            'user' => [
                'code' => 30,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有user数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade::syncUser();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade::syncUserAomunt();

                    $this->consoleOutput('批量执行所有user数据同步ES结束--1', 'info');
                }
            ],
            'admin' => [
                'code' => 40,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有admin数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacade::syncAdmin();

                    $this->consoleOutput('批量执行所有admin数据同步ES结束--1', 'info');
                }
            ],

            'youhu_auth_service' => [
                'code' => 50,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有youhu_auth_services数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncYouhuAuthServiceFacade::syncYouhuAuthService();

                    $this->consoleOutput('批量执行所有youhu_auth_services数据同步ES结束--1', 'info');
                }
            ],
            'album' => [
                'code' => 60,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有album数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbums();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade::syncAlbumPictures();

                    $this->consoleOutput('批量执行所有album数据同步ES结束--1', 'info');
                }
            ],

            'union' => [
                'code' => 70,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有unions数据同步ES--1', 'info');

                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncRolePermissionUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserLevelItemUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserRoleUnions();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade::syncUserSourceUnions();

                    $this->consoleOutput('批量执行所有unions数据同步ES结束--1', 'info');
                }
            ],
            'debug' => [
                'code' => 1000000,
                'handler' => function () {
                    $this->consoleOutput('开始批量执行所有测试数据同步ES--1', 'info');
                    //\App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncPermission();
                    \App\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade::syncSystemWechatConfig();
                    $this->consoleOutput('批量执行所有测试数据同步ES结束--1', 'info');
                }
            ]
        ];
    }

    // 执行计算方法
    public function run($indexName)
    {
        // 校验操作类型是否存在
        if (!isset($this->indexMap[$indexName])) {
            plog(['error' => 'IndexNotFoundError', 'indexName' => $indexName], 'EsSyncDataFacadeService', 'runError');
            $this->consoleOutput('索引未找到: ' . $indexName, 'error');
            throw new CommonException('IndexNotFoundError');
        }

        $config = $this->indexMap[$indexName];
        // 数字标识判断（保证性能）
        if ($config['code'] < 0) {
            plog(['error' => 'IndexMapCodeNotFoundError', 'indexName' => $indexName], 'EsSyncDataFacadeService', 'runError');
            $this->consoleOutput('索引映射码未找到: ' . $indexName, 'error');
            throw new CommonException('IndexMapCodeNotFoundError');
        }

        // 绑定闭包上下文并执行
        $handler = $config['handler']->bindTo($this, $this);
        return $handler();
    }

    //执行同步数据
    public function runSyncData($indexName)
    {
        $this->run($indexName);
    }
}
