<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-03 16:37:39
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-03 16:37:57
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Sync\Album;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Sync\Album\EsSyncAlbumFacadeService
 */
class EsSyncAlbumFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsSyncAlbumFacade";
    }

    public static function syncAlbums()
    {
        static::getFacadeRoot()->syncAlbums();
    }

    public static function syncAlbumPictures()
    {
        static::getFacadeRoot()->syncAlbumPictures();
    }
}
