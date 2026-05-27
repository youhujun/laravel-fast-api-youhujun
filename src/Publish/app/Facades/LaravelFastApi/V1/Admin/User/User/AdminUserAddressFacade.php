<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-10 00:25:02
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacadeService
 */
class AdminUserAddressFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserAddressFacade";
    }
}
