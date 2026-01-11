<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 10:40:52
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:41:45
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\Login;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\Login\PhoneLoginFacadeService
 */
class PhoneLoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneLoginFacade";
    }
}
