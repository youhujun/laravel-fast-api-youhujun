<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 21:54:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-05-05 21:54:25
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\System\Permission;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\System\Permission\AdminPermissionFacadeService
 */
class AdminPermissionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminPermissionFacade";
    }
}
