<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 15:32:48
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 15:35:21
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\Es\EsTestFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Test\V1\Es;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\Es\EsTestFacadeService
 */
class EsTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsTestFacade";
    }
}
