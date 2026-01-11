<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-10 21:42:10
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:23:55
 * @FilePath: \app\Service\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Service\Contract\LaravelFastApi\V1\Common\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract;

/**
 * @see \App\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract
 */
class BaseAddUserHandlerContractService implements AddUserHandlerContract
{
    public function handle($param)
    {
        p('基础实现');
    }
}
