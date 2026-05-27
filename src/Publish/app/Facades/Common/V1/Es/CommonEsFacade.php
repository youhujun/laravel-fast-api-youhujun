<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-27 01:15:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-27 01:28:33
 * @FilePath: \youhu-laravel-api-12\app\Facades\Common\V1\Es\CommonEsFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Common\V1\Es;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Es\CommonEsFacadeService
 */
class CommonEsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "CommonEsFacade";
    }

    public static function getEsSystemConfig()
    {
        return static::getFacadeRoot()->getEsSystemConfig();
    }
}
