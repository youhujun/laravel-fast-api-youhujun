<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:06:04
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facade\LaravelFastApi\V1\Admin\Develop;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacadeService
 */
class DeveloperFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "DeveloperFacade";
    }
}
