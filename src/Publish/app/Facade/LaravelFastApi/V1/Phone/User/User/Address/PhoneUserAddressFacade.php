<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-04 08:16:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-04 08:18:09
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\User\User\Address;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacadeService
 */
class PhoneUserAddressFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneUserAddressFacade";
    }
}
