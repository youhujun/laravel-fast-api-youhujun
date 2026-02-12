<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-28 16:36:02
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Websocket;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Websocket\PhoneSocketFacadeService
 */
class PhoneSocketFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneSocketFacade";
    }
}
