<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-02 12:26:59
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-02 23:08:59
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Album;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Album\EsCreateAlbumIndexFacadeService
 */
class EsCreateAlbumIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateAlbumIndexFacade";
    }

	public static function createAlbumsIndex()
	{
		return static::getFacadeRoot()->createAlbumsIndex();
	}

	public static function createAlbumPicturesIndex()
	{
		return static::getFacadeRoot()->createAlbumPicturesIndex();
	}
}
