<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 12:50:24
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-12 12:50:48
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\Notify;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\Notify\PhonePayNotifyFacadeService
 */
class PhonePayNotifyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhonePayNotifyFacade";
    }
}
