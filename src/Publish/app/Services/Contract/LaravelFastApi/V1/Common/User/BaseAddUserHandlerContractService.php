<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-10 21:42:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-23 15:43:18
 * @FilePath: \youhu-laravel-api-12\app\Services\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Services\Contract\LaravelFastApi\V1\Common\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract;
use App\DTOs\Contracts\V1\User\User\AddUserHandlerContractDTO;
/**
 * @see \App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract
 */
class BaseAddUserHandlerContractService implements AddUserHandlerContract
{
    public function handle(AddUserHandlerContractDTO $businessContractDTO): void
    {
        plog(['info' => '基础添加用户处理器','$businessContractDTO' => $businessContractDTO], 'BaseAddUserHandlerContractService', 'handle');
    }
}
