<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer & codebuddy
 * @Date: 2026-03-30 00:09:23
 * @LastEditors: youhujun youhu8888@163.com & xueer & codebuddy
 * @LastEditTime: 2026-03-30 00:09:51
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\Ms\MsTestFacade.php
 * Copyright (C) 2026 youhujun & xueer & codebuddy. All rights reserved.
 */

namespace App\Facades\Test\V1\Ms;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\Ms\MsTestFacadeService
 */
class MsTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "MsTestFacade";
    }
}
