<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-13 20:26:45
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-13 23:13:27
 * @FilePath: \app\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacade.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Facade\LaravelFastApi\V1\Admin\System\SystemConfig;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Service\Facade\LaravelFastApi\V1\Admin\System\SystemConfig\AdminVoiceConfigFacadeService
 */
class AdminVoiceConfigFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "AdminVoiceConfigFacade";
    }
}
