<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-07 08:09:43
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-11 09:23:54
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent\AddDeveloperAdminListener.php
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\Develop\AddDeveloperEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\Admin\Admin;
/**
 * @see  \App\Events\Admin\Develop\AddDeveloperEvent
 */
class AddDeveloperAdminListener
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
        $adminObject = $event->admin;
        $userObject = $event->user;
        $validated = $event->validated;

        $adminObject = new Admin;

        $adminObject->user_id = $userObject->id;
        $adminObject->userId = $userObject->userId;
        $adminObject->account_name = $userObject->account_name;
        $adminObject->password = $userObject->password;
        //默认是可用的
        $adminObject->switch = 1;
        $adminObject->created_at = time();
        $adminObject->created_time = time();

        $result = $adminObject->save();

        if(!$result)
        {
            throw new CommonException('AddDevelopAdminError');
        }

    }
}
