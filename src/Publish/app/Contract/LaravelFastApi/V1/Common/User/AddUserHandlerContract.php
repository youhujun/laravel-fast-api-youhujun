<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-10 22:16:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:16:38
 * @FilePath: \App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Contracts\LaravelFastApi\V1\Common\User;

/**
 * @see \App\Services\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService
 * @see \App\Services\Contract\YouHu\V1\Common\User\YouHuAddUserHandlerContractService
 */

interface AddUserHandlerContract
{
    /**
    *
    * @param $param
    */

    public function handle($param);
}
