<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-06-04 13:48:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-06-04 14:06:40
 * @FilePath: \youhu-laravel-api-13\app\Facades\Test\V1\Attributes\AttributeTestFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\Test\V1\Attributes;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Test\V1\Attributes\AttributeTestFacadeService
 */
class AttributeTestFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AttributeTestFacade";
    }
}
