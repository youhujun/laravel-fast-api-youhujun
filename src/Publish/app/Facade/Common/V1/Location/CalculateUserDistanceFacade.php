<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-29 15:47:45
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-29 15:56:19
 * @FilePath: \App\Facades\Common\V1\Location\CalculateUserDistanceFacade.php
 */

namespace App\Facades\Common\V1\Location;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Common\V1\Location\CalculateUserDistanceFacadeService
 */
class CalculateUserDistanceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "CalculateUserDistanceFacade";
    }

	
}
