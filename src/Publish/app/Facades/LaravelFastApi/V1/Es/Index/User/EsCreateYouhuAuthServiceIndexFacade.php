<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 03:56:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 00:15:04
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\User\EsCreateYouhuAuthServiceIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\User\EsCreateYouhuAuthServiceIndexFacadeService
 */
class EsCreateYouhuAuthServiceIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateYouhuAuthServiceIndexFacade";
    }

    public static function createYouhuAuthServiceIndex(): void
    {
        static::getFacadeRoot()->createYouhuAuthServiceIndex();
    }
}
