<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-20 23:10:20
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-05-20 23:10:26
 * @FilePath: \App\Facades\LaravelFastApi\V1\Admin\File\AdminUploadFacade.php
 */

namespace App\Facades\LaravelFastApi\V1\Admin\File;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\File\AdminUploadFacadeService
 */
class AdminUploadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminUploadFacade";
    }
}
