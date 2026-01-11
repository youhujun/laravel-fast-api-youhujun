<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-16 18:51:10
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:45:02
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\Websocket\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\Websocket\User\PhoneSocketUserFacadeService
 */
class PhoneSocketUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneSocketUserFacade";
    }
}
