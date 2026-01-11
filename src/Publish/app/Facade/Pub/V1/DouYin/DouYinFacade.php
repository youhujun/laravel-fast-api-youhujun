<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-24 15:25:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-05-20 17:36:20
 * @FilePath: \app\Facade\Pub\V1\DouYin\DouYinFacade.php
 */

namespace App\Facade\Pub\V1\DouYin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\Pub\V1\DouYin\DouYinFacadeService
 */
class DouYinFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "DouYinFacade";
    }
}
