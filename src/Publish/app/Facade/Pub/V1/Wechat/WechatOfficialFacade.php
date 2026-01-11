<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-07 10:25:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:48:31
 * @FilePath: \app\Facade\Pub\V1\Wechat\WechatOfficialFacade.php
 */

namespace App\Facade\Pub\V1\Wechat;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\Pub\V1\Wechat\WechatOfficialFacadeService
 */
class WechatOfficialFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "WechatOfficialFacade";
    }
}
