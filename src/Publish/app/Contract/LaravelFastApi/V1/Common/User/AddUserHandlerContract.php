<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-10 22:16:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 22:16:38
 * @FilePath: \app\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace App\Contract\LaravelFastApi\V1\Common\User;

/**
 * @see \App\Service\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService
 * @see \App\Service\Contract\YouHu\V1\Common\User\YouHuAddUserHandlerContractService
 */

interface AddUserHandlerContract
{
    /**
    *
    * @param $param
    */

    public function handle($param);
}
