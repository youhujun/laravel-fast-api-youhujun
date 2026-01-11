<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-03 10:42:32
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 10:55:51
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserAmountListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\User\Info\UserAmount;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
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
