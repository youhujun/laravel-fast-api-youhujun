<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-04 00:23:42
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-04 00:28:43
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\User\Admin\AdministratorFacadeService
 */
class AdministratorFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdministratorFacade";
    }
}
