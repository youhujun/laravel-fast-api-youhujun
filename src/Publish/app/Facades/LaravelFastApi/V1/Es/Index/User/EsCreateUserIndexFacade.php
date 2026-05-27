<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-25 02:44:50
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-25 03:30:35
 * @FilePath: \youhu-laravel-api-12\app\Facades\LaravelFastApi\V1\Es\Index\EsCreateUserIndexFacade.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Es\Index\User;

use Illuminate\Support\Facades\Facade;

class EsCreateUserIndexFacade extends Facade
{
    /**
     * @see \App\Services\Facade\LaravelFastApi\V1\Es\Index\User\EsCreateUserIndexFacadeService
     */
    protected static function getFacadeAccessor()
    {
        return "EsCreateUserIndexFacade";
    }

    public static function createUsersIndex(): void
    {
        static::getFacadeRoot()->createUsersIndex();
    }
}
