<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 16:34:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 00:06:28
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Log\EsCreateApiLogIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Log;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Log\EsCreateApiLogIndexFacadeService
 */
class EsCreateApiLogIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateApiLogIndexFacade";
    }
}
