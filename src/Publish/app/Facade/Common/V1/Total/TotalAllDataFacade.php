<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-17 03:09:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-17 03:09:58
 * @FilePath: \app\Facade\Common\V1\Total\TotalAllDataFacade.php
 */

namespace App\Facade\Common\V1\Total;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\Common\V1\Total\TotalAllDataFacadeService
 */
class TotalAllDataFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "TotalAllDataFacade";
    }
}
