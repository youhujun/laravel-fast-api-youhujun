<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:46:25
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:12:23
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\Service\Group;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminLabelFacadeService
 */
class AdminLabelFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminLabelFacade";
    }
}
