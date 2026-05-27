<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-24 15:14:16
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-24 15:20:24
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\System\SystemConfigTestFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Test\V1\System;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\System\SystemConfigTestFacadeService
 */
class SystemConfigTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "SystemConfigTestFacade";
    }
}
