<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-28 18:18:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-28 18:19:58
 * @FilePath: \App\Facades\Pub\V1\Sms\SmsFacade.php
 */

namespace App\Facades\Pub\V1\Sms;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Pub\V1\Sms\SmsFacadeService
 */
class SmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "SmsFacade";
    }
}
