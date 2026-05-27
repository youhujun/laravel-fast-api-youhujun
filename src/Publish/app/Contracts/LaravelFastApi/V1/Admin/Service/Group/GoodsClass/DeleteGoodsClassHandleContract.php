<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-09 15:30:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 16:56:10
 * @FilePath: \youhu-laravel-api-12\app\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassHandleContract.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass;

/**
 * @see \App\Services\Contract\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\BaseDeleteGoodsClassContractServiceService
 * @see \App\Services\Contract\YouHuShop\V1\Admin\Service\Group\GoodsClass\YouHuShopDeleteGoodsClassContractServiceService
 */
interface DeleteGoodsClassHandleContract
{
    /**
    *
    * @param $handleParamArray
    */

    public function handle(array $handleParamArray);
}
