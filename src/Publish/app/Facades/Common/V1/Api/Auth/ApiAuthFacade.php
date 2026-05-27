<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-20 21:34:03
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-20 21:57:19
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Api\Auth\ApiAuthFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Common\V1\Api\Auth;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Api\Auth\ApiAuthFacadeService
 */
class ApiAuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "ApiAuthFacade";
    }
}
