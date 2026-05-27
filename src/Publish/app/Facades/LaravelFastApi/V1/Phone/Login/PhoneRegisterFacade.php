<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 09:58:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 10:12:35
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Login;

use Illuminate\Support\Facades\Facade;

/**
 *  @see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\PhoneRegisterFacadeService
 */
class PhoneRegisterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneRegisterFacade";
    }
}
