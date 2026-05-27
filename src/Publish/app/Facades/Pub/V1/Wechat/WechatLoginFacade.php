<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-28 14:19:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-28 14:19:41
 * @FilePath: \App\Facades\Pub\V1\Wechat\WechatLoginFacade.php
 */

namespace App\Facades\Pub\V1\Wechat;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Pub\V1\Wechat\WechatLoginFacadeService
 */
class WechatLoginFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "WechatLoginFacade";
    }
}
