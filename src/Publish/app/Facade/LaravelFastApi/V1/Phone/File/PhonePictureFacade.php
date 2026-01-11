<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-11 09:10:04
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-11 09:10:08
 * @FilePath: \app\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacade.php
 */

namespace App\Facade\LaravelFastApi\V1\Phone\File;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacadeService
 */
class PhonePictureFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhonePictureFacade";
    }
}
