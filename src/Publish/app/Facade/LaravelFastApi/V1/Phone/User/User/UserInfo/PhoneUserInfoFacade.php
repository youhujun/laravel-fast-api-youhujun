<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-05 10:35:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-05 10:35:21
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\User\User\UserInfo\PhoneUserInfoFacadeService
 */
class PhoneUserInfoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneUserInfoFacade";
    }
}
