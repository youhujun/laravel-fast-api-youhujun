<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 16:16:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 10:14:05
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\System\PhoneMapFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\System;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\System\PhoneMapFacadeService
 */
class PhoneMapFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneMapFacade";
    }
}
