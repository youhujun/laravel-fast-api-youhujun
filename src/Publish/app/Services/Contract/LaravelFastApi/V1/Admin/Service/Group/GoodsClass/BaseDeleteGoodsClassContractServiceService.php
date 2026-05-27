<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-09 15:37:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 16:56:27
 * @FilePath: \youhu-laravel-api-12\app\Services\Contract\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\BaseDeleteGoodsClassContractServiceService.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace App\Services\Contract\LaravelFastApi\V1\Admin\Service\Group\GoodsClass;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use App\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassHandleContract;

/**
 * @see \App\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassHandleContract
 */
class BaseDeleteGoodsClassContractServiceService implements DeleteGoodsClassHandleContract
{
    public function handle(array $handleParamArray)
    {
        plog(['info' => '基础组件包删除商品分类','$handleParamArray' => $handleParamArray], 'BaseDeleteGoodsClassContractServiceService', 'handle');
    }
}
