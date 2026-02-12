<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 18:13:57
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Facades\LaravelFastApi\V1\Admin\System\Role;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\System\Role\AdminRoleFacadeService
 */
class AdminRoleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminRoleFacade";
    }
}
