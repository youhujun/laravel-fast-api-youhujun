<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-31 22:34:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-31 23:45:27
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateUseLogIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Log;

use Illuminate\Support\Facades\Facade;

use function Symfony\Component\String\s;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Log\EsCreateUserLogIndexFacadeService
 */
class EsCreateUserLogIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateUserLogIndexFacade";
    }

    public static function createUserEventLogsIndex()
    {
        return static::getFacadeRoot()->createUserEventLogsIndex();
    }

    public static function createUserLoginLogsIndex()
    {
        return static::getFacadeRoot()->createUserLoginLogsIndex();
    }

    public static function createUserAmountLogsIndex()
    {
        return static::getFacadeRoot()->createUserAmountLogsIndex();
    }

    public static function createUserCoinLogsIndex()
    {
        return static::getFacadeRoot()->createUserCoinLogsIndex();
    }

    public static function createUserScoreLogsIndex()
    {
        return static::getFacadeRoot()->createUserScoreLogsIndex();
    }

    public static function createUserRealAuthLogsIndex()
    {
        return static::getFacadeRoot()->createUserRealAuthLogsIndex();
    }

    public static function createUserUploadFileLogsIndex()
    {
        return static::getFacadeRoot()->createUserUploadFileLogsIndex();
    }

    public static function createUserLocationLogsIndex()
    {
        return static::getFacadeRoot()->createUserLocationLogsIndex();
    }
}
