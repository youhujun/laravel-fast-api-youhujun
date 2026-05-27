<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-30 00:16:28
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 00:19:55
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\XueHu\XueHuTestFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\Test\V1\XueHu;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\XueHu\XueHuTestFacadeService
 */
class XueHuTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "XueHuTestFacade";
    }
}
