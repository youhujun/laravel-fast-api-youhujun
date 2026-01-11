<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-27 15:59:34
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-17 18:58:17
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent\AddPhoneUserLogListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserLoginEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Phone\CommonException;


use App\Models\LaravelFastApi\V1\User\Log\UserLoginLog;

/**
 * @see \App\Events\Phone\User\UserLoginEvent
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
        $userLoginLog->status = 10;
        $userLoginLog->ip = Request::getClientIp();
        $userLoginLog->instruction = '用户登录';
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
