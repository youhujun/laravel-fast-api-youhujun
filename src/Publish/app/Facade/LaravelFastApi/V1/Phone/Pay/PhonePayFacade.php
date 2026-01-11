<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 09:14:49
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-12 09:14:58
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\Pay;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\Pay\PhonePayFacadeService
 */
class PhonePayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhonePayFacade";
    }
}
