<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 14:49:20
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 16:33:28
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Sync\System;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Sync\System\EsSyncSystemFacadeService
 */
class EsSyncSystemFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncSystemFacade";
    }
}
