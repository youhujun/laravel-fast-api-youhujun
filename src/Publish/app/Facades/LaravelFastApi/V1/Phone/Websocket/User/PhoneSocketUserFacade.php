<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-16 18:51:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:45:02
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Websocket\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacadeService
 */
class PhoneSocketUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneSocketUserFacade";
    }
}
