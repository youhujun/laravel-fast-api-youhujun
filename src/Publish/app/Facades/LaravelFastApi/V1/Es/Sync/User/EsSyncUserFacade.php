<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-20 14:15:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 01:24:08
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Sync\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Sync\User\EsSyncUserFacadeService
 */
class EsSyncUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncUserFacade";
    }
}
