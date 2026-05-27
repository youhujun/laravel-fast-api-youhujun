<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-06 20:33:33
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-06 20:34:12
 * @FilePath: \App\Facades\Pub\V1\Qrcode\PubQrcodeFacade.php
 */

namespace App\Facades\Pub\V1\Qrcode;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Pub\V1\Qrcode\PubQrcodeFacadeService
 */
class PubQrcodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PubQrcodeFacade";
    }
}
