<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 05:48:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 05:50:44
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\EsCreateAdminIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\User\EsCreateAdminIndexFacadeService
 */
class EsCreateAdminIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateAdminIndexFacade";
    }
}
