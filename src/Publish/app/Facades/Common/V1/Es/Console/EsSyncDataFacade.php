<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 01:11:13
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-20 13:59:48
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Es\Console\EsSyncDataFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Common\V1\Es\Console;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Es\Console\EsSyncDataFacadeService
 */
class EsSyncDataFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncDataFacade";
    }
}
