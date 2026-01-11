<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-02 11:46:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:56:26
 * @FilePath: \app\Contract\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Contract\LaravelFastApi\V1\Phone\Order;

use Illuminate\Support\Facades\App;

/**
 * @see \App\Service\Contract\LaravelFastApi\V1\Phone\Order\BaseOrderPaymentHandlerContractService
 * @see  \App\Service\Contract\YouHu\V1\Phone\Order\YouHuOrderPaymentHandlerContractService
 */
interface OrderPaymentHandlerContract
{
    /**
    *
    * @param $param
    */

    public function handle($param);
}
