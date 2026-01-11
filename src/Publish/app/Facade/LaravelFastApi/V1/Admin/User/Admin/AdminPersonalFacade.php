<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-08 14:10:45
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 14:10:57
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacadeService
 */
namespace App\Facade\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Support\Facades\Facade;

class AdminPersonalFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminPersonalFacade";
    }
}
