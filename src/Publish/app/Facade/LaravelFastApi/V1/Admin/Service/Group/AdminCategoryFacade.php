<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-26 10:46:02
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 11:11:56
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\Service\Group;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Service\Group\AdminCategoryFacadeService
 */
class AdminCategoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminCategoryFacade";
    }
}
