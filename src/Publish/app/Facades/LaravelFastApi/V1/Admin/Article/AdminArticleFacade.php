<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-12 21:10:39
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Admin\Article\AdminArticleFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Facades\LaravelFastApi\V1\Admin\Article;

use Illuminate\Support\Facades\Facade;

/***
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacadeService
 */
class AdminArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminArticleFacade";
    }
}
