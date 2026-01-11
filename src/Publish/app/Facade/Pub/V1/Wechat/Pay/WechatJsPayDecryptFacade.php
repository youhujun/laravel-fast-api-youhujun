<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-12 13:12:32
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:37:46
 * @FilePath: \app\Facade\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacade.php
 */

namespace App\Facade\Pub\V1\Wechat\Pay;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\Pub\V1\Wechat\Pay\WechatJsPayDecryptFacadeService
 */
class WechatJsPayDecryptFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "WechatJsPayDecryptFacade";
    }
}
