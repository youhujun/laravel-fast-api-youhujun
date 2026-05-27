<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-01 16:43:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-01 16:45:26
 * @FilePath: \youhu-laravel-api-12\app\Facades\Template\V1\Replace\EsReplaceFacade.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Facades\Template\V1\Replace;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Facade\Template\V1\Replace\EsReplaceFacadeService
 */
class EsReplaceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "EsReplaceFacade";
    }
}
