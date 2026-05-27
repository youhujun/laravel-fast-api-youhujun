<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 03:33:13
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-02 23:23:20
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\System;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\System\EsCreateSystemIndexFacadeService
 */
class EsCreateSystemIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateSystemIndexFacade";
    }

    public static function createSystemConfigIndex(): void
    {
        static::getFacadeRoot()->createSystemConfigIndex();
    }
}
