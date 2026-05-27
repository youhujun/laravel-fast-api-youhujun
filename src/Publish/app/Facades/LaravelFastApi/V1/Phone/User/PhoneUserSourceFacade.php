<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-03 09:46:04
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-03 09:46:18
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacadeService
 */
class PhoneUserSourceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneUserSourceFacade";
    }
}
