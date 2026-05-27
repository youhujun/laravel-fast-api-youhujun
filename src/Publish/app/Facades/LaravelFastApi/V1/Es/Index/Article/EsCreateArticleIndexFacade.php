<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 02:38:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 02:41:34
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\Article;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\Article\EsCreateArticleIndexFacadeService
 */
class EsCreateArticleIndexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsCreateArticleIndexFacade";
    }

    public static function createArticlesIndex()
    {
        return static::getFacadeRoot()->createArticlesIndex();
    }
}
