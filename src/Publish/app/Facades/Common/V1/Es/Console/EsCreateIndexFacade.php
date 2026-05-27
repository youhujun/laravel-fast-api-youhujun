<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-19 17:22:40
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-19 17:22:48
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Es\EsCreateIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Common\V1\Es\Console;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Es\Console\EsCreateIndexFacadeService
 */
class EsCreateIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateIndexFacade";
    }
}
