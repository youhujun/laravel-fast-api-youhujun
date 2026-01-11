<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-02 18:17:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-02 18:18:37
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\Log\AdminUserLogFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Admin\Log;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\Log\AdminUserLogFacadeService
 */
class AdminUserLogFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUserLogFacade";
    }
}
