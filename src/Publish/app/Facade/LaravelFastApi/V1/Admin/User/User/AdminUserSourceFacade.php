<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-06 09:24:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-06 09:27:05
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserSourceFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserSourceFacadeService
 */
class AdminUserSourceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserSourceFacade";
    }
}
