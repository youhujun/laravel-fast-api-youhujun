<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-23 13:48:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-23 13:50:03
 * @FilePath: \youhu-laravel-api-12\app\Facades\Test\V1\User\UserTestFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\Test\V1\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\User\UserTestFacadeService
 */
class UserTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "UserTestFacade";
    }
}
