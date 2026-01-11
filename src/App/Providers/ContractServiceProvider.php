<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-03 10:20:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-10 23:01:39
 * @FilePath: \src\App\Providers\ContractServiceProvider.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace YouHuJun\LaravelFastApi\App\Providers;

use Illuminate\Support\ServiceProvider;

use \App\Contract\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContrat;

class ContractServiceProvider extends ServiceProvider
{
	 public function register()
	 {

	 	$this->bindContract(
			\App\Contract\LaravelFastApi\V1\Common\User\AddUserHandlerContract::class,
			'add_user_handler',
			\App\Service\Contract\LaravelFastApi\V1\Common\User\BaseAddUserHandlerContractService::class
		);
		
		$this->bindContract(
			\App\Contract\LaravelFastApi\V1\Phone\Order\OrderPaymentHandlerContract::class,
			'order_payment_handler',
			\App\Service\Contract\LaravelFastApi\V1\Phone\Order\BaseOrderPaymentHandlerContractService::class
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
		$this->app->bind($interface, function($app) use ($interface, $configKey, $defaultImpl) {
			return resolve_with_validation(
				$app,
				config("youhu.{$configKey}"),
				$interface,
				$defaultImpl
			);
		});
	}

}