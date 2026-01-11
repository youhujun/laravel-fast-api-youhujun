<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-31 15:27:03
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-31 15:27:29
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemWechatConfigFacadeService
 */
class SystemWechatConfigFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "SystemWechatConfigFacade";
    }
}
