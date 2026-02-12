<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-03 16:48:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-03 18:59:44
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\System\GoodsClass\PhoneGoodsClassFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\System\GoodsClass;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\System\GoodsClass\PhoneGoodsClassFacadeService
 */
class PhoneGoodsClassFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneGoodsClassFacade";
    }
}
