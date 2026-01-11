<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-02 18:13:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 10:13:54
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\System\Region;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\System\Region\PhoneRegionFacadeService
 */
class PhoneRegionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneRegionFacade";
    }
}
