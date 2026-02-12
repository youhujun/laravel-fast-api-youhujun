<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-11 09:10:04
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-07-11 09:10:08
 * @FilePath: \App\Facades\LaravelFastApi\V1\Phone\File\PhonePictureFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Phone\File;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Phone\File\PhonePictureFacadeService
 */
class PhonePictureFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "PhonePictureFacade";
    }
}
