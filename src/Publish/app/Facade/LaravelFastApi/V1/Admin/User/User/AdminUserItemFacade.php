<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:43:46
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-09 19:44:03
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Support\Facades\Facade;

/**
 * @see  \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacadeService
 */
class AdminUserItemFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserItemFacade";
    }
}
