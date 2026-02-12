<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 11:32:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 11:32:29
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Admin\Login\Common;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Login\Common\AdminBackgroundLoginFacadeService
 */
class AdminBackgroundLoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminBackgroundLoginFacade";
    }
}
