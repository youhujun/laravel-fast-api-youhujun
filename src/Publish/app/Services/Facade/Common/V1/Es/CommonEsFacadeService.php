<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-27 01:15:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-27 01:31:19
 * @FilePath: \youhu-laravel-api-12\app\Services\Facade\Common\V1\Es\CommonEsFacadeService.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Services\Facade\Common\V1\Es;

use App\Exceptions\Common\CommonException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use YouHuJun\Tool\App\Facades\V1\Es\EsFacade;
use App\Facades\Common\V1\Es\EsQueryFacade;
use App\Models\LaravelFastApi\V1\System\SystemConfig;

/**
 * @see \App\Facades\Common\V1\Es\CommonEsFacade
 */
class CommonEsFacadeService
{
    public function test()
    {
        echo "CommonEsFacadeService test";
    }

    public function __construct()
    {
    }

    /**
     * 获取ES系统配置
     *
     * @return Collection
     */
    public function getEsSystemConfig(): Collection
    {
        $systemConfigCollection = collect();

        $indexName = config('common_es.indices.system.system_configs');

        $esQuery = EsQueryFacade::index($indexName);

        $esQuery->whereNull('deleted_at');

        $max_size = config('common_es.max_result_window');

        $systemConfigArray  = $esQuery->limit($max_size)->get()->toArray();

        $systemConfigCollection = collect($systemConfigArray)->map(function ($item) {
            return (new SystemConfig())->fill((array)$item);
        });

        return $systemConfigCollection;
    }
}
