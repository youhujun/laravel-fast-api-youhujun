<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 15:44:57
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-21 15:45:11
 * @FilePath: \app\Facade\Common\V1\User\User\CommonUserFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facade\Common\V1\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\Common\V1\User\User\CommonUserFacadeService
 */
class CommonUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "CommonUserFacade";
    }
}
