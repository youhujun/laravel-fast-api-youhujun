<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 23:02:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 23:02:57
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Sync\Union;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Sync\Union\EsSyncUnionFacadeService
 */
class EsSyncUnionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncUnionFacade";
    }
}
