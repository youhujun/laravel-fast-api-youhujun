<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 11:09:00
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facade\LaravelFastApi\V1\Admin\Login;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Login\AdminLoginFacadeService
 */
class AdminLoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminLoginFacade";
    }
}
