<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-01 20:57:04
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 12:01:47
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserRoleListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent
 */
class AddUserRoleListener
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

        $data = [];
        $date = date('Y-m-d H:i:s',time());
        $time = time();
        $user_id = $user->id;

        $data[0] = ['created_at' =>$date,'created_time'=>$time,'user_id'=>$user_id,'role_id'=>4];

        $userRoleUnionResult = UserRoleUnion::insert($data);

        if(!$userRoleUnionResult)
        {
            if($isTransation)
            {
                DB::rollBack();
            }
            throw new CommonException('AddUserRoleError');
        }
    }
}
