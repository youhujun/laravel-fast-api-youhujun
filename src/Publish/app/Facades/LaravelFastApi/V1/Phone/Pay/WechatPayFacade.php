<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 08:18:45
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-12 08:20:39
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Pay\WechatPayFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Pay;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Pay\WechatPayFacadeService
 */
class WechatPayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "WechatPayFacade";
    }
}
