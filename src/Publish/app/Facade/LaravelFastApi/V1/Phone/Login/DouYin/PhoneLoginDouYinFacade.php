<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-24 17:00:59
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-24 17:02:23
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\Login\DouYin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\Login\DouYin\PhoneLoginDouYinFacadeService
 */
class PhoneLoginDouYinFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhoneLoginDouYinFacade";
    }
}
