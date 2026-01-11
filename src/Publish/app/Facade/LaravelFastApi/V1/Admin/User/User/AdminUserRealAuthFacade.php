<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-19 22:02:46
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-19 22:02:52
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserRealAuthFacadeService
 */
class AdminUserRealAuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserRealAuthFacade";
    }
}
