<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-03 11:58:42
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 11:59:05
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserAmountListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserAmount;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent
 */
class AddUserAmountListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        $validated = $event->validated;
        $isTransation = $event->isTransation;

		$userAmount = new UserAmount;

		$userAmount->user_id = $user->id;
		$userAmount->userId = $user->userId;
		$userAmount->created_at = time();
		$userAmount->created_time = time();
		$userAmount->sort =100;

		$userAmountResult = $userAmount->save();

		if(!$userAmountResult){
			if($isTransation)
			{
				DB::rollBack();
			}

			throw new CommonException('AddUserAmountError');
		}
    }
}
