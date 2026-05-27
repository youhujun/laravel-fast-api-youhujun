<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-03 16:45:30
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 16:45:42
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\Album\AlbumTestFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\Test\V1\Album;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\Album\AlbumTestFacadeService
 */
class AlbumTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AlbumTestFacade";
    }
}
