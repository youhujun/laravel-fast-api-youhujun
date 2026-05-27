<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-22 14:49:36
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-22 14:50:16
 * @FilePath: \App\Facades\Common\V1\Login\CommonLoginFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facades\Common\V1\Login;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Login\CommonLoginFacadeService
 */
class CommonLoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "CommonLoginFacade";
    }
}
