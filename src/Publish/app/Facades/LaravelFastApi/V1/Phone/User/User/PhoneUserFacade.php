<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-14 17:40:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-04 16:05:52
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\User\User\PhoneUserFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\User\User\PhoneUserFacadeService
 */
class PhoneUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneUserFacade";
    }
}
