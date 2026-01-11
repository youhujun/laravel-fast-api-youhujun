<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-10 09:40:04
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-20 11:41:10
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent\AddPhoneUserLogListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserLogoutEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserLogoutEvent
 */
class AddPhoneUserLogListener
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
        $isTransation = $event->isTransation;

        $userLoginLog = new UserLoginLog;

        $userLoginLog->user_id = $user->id;
        $userLoginLog->status = 20;
        $userLoginLog->ip = Request::getClientIp();
        $userLoginLog->instruction = '用户退出';
        $userLoginLog->source_type = 10;
        $userLoginLog->created_at = time();
        $userLoginLog->created_time = time();

        $userLoginLogResult = $userLoginLog->save();

		if(!$userLoginLogResult)
		{
            if($isTransation)
            {
                DB::rollBack();
            }

			throw new CommonException('AddPhoneUserLoginLogError');
		}
    }
}
