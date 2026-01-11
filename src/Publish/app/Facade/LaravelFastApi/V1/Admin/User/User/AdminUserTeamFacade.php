<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:49:00
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 15:39:45
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserTeamFacadeService
 */
class AdminUserTeamFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserTeamFacade";
    }
}
