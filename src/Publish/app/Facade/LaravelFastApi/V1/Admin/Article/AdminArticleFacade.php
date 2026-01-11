<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:33:46
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Facade\LaravelFastApi\V1\Admin\Article;

use Illuminate\Support\Facades\Facade;

/***
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Article\AdminArticleFacadeService
 */
class AdminArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminArticleFacade";
    }
}
