<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 22:36:08
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:12:57
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\Service\Level;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Service\Level\AdminUserLevelFacadeService
 */
class AdminUserLevelFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserLevelFacade";
    }
}
