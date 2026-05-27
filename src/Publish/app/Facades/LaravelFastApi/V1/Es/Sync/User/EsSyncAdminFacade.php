<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 05:53:54
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 01:23:46
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Sync\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Sync\User\EsSyncAdminFacadeService
 */
class EsSyncAdminFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncAdminFacade";
    }
}
