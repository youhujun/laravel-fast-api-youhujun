<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-03 10:20:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-09 15:57:09
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\App\Providers\ContractServiceProvider.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContrat;

class ContractServiceProvider extends ServiceProvider
{
    public function register()
    {
        //用户注册
        $this->bindContract(
            \App\Contracts\LaravelFastApi\V1\Common\User\AddUserHandlerContract::class,
            'youhu.add_user_handler',
            \App\Services\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService::class
        );

        //支付订单
        $this->bindContract(
            \App\Contracts\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract::class,
            'youhu.order_payment_handler',
            \App\Services\Contract\LaravelFastApi\V1\Phone\Order\BaseOrderPaymentHandlerContractService::class
        );

        //删除商品分类
        $this->bindContract(
            \App\Contracts\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\DeleteGoodsClassHandleContract::class,
            'youhushop.delete_goods_class',
            \App\Services\Contract\LaravelFastApi\V1\Admin\Service\Group\GoodsClass\BaseDeleteGoodsClassContractServiceService::class
        );
    }

    /**
    * Bootstrap services.
    *
    * @return void
    */
    public function boot()
    {
    }

    private function bindContract(string $interface, string $configKey, string $defaultImpl)
    {
        $this->app->bind($interface, function ($app) use ($interface, $configKey, $defaultImpl) {
            return resolve_with_validation(
                $app,
                config("common_contract.{$configKey}"),
                $interface,
                $defaultImpl
            );
        });
    }
}
