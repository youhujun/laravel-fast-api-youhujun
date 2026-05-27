<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-31 22:34:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 01:36:01
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Log;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Log\EsCreateAdminLogIndexFacadeService
 */
class EsCreateAdminLogIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateAdminLogIndexFacade";
    }

    public static function createAdminEventLogsIndex()
    {
        return static::getFacadeRoot()->createAdminEventLogsIndex();
    }

	public static function createAdminLoginLogsIndex()
    {
        return static::getFacadeRoot()->createAdminLoginLogsIndex();
    }
}
