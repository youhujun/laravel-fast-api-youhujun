<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-24 15:14:18
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-31 23:19:51
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Test\V1\System\SystemConfigTestFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Test\V1\System;

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
use App\Models\LaravelFastApi\V1\System\SystemConfig;
use App\Facades\LaravelFastApi\V1\Es\Sync\EsSyncSystemConfigFacade;
use App\Facades\Common\V1\Es\CommonEsFacade;

/**
 * @see \App\Facades\Test\V1\System\SystemConfigTestFacade
 */
class SystemConfigTestFacadeService
{
    public function test()
    {
        echo "SystemConfigTestFacadeService test";

        //$this->testGetEsSystemConfig();

        //$systemConfigCollection = CommonEsFacade::getEsSystemConfig();

        //p($systemConfigCollection);
    }

    //测试同步系统配置
    public function syncSystemConfig()
    {
        EsSyncSystemConfigFacade::syncSystemConfig();
    }

    public function testGetEsSystemConfig()
    {
        p('测试获取ES系统配置');


        $indexName = config('common_es.indices.system_configs');

        // 精确匹配搜索
        $query = [
            'match_all' => new \stdClass()
        ];

        $result = EsFacade::searchDoc($indexName, $query);

        //p($result);

        $systemConfigArray = [];

        if (isset($result['code']) && $result['code'] == 0 && isset($result['data']['hits']['total']['value']) && $result['data']['hits']['total']['value'] > 0) {
            $systemConfigPreArray = $result['data']['hits']['hits'];

            foreach ($systemConfigPreArray as $systemConfigPreItemArray) {
                $systemConfigArray[] = $systemConfigPreItemArray['_source'];
            }
        }


        $systemConfigCollection = collect($systemConfigArray)->map(function ($item) {
            return (new SystemConfig())->fill($item);
        });

        p($systemConfigCollection);
    }
}
