<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-02 11:47:25
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:56:46
 * @FilePath: \App\Services\Contract\LaravelFastApi\V1\Phone\Order\BaseOrderPaymentHandlerContractService.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Services\Contract\LaravelFastApi\V1\Phone\Order;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\App;
use App\Contracts\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract;

/**
 * @see \App\Contracts\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract
 */
class BaseOrderPaymentHandlerContractService implements OrderPaymentHandlerContract
{
    public function handle($param)
    {
        //基础继承:暂时打印日志
        plog(['param' => $param], 'BasePaymentHandler', 'PamentHandler');
    }
}
