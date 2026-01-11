<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-09-11 14:30:31
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent\UpdateUserRealAuthApplyListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\CheckUserRealAuthEvent
 */
class UpdateUserRealAuthApplyListener
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
        $admin = $event->admin;
        $validated = $event->validated;

        $userApplayRealAuth = UserRealAuthLog::find($validated['id']);

		if(!$userApplayRealAuth)
		{
			throw new CommonException('ThisDataNotExistsError');
		}
		else
		{
			if($userApplayRealAuth->status !== 10)
            {
                throw new CommonException('RealAuthApplyHasError');
            }

            $userApplayRealAuthRevision = $userApplayRealAuth->revision;

            if($validated['is_real_auth'])
            {
                $userApplayRealAuthUpdateData = ['status'=> 20,'updated_time'=>time(),'updated_at'=>\date('Y-m-d H:i:s',time()),'revision'=>$userApplayRealAuthRevision + 1];
            }
            else
            {
                 $userApplayRealAuthUpdateData = ['status'=> 30,'updated_time'=>time(),'updated_at'=>\date('Y-m-d H:i:s',time()),'revision'=>$userApplayRealAuthRevision + 1,'refuse_info'=>$validated['refuse_info']];
            }

            $where[] = ['revision','=',$userApplayRealAuthRevision];
            $where[] = ['id','=',$validated['id']];

            $userApplayRealAuthResult = UserRealAuthLog::where($where)->update($userApplayRealAuthUpdateData);

            if(!$userApplayRealAuthResult)
            {
                throw new CommonException('RealAuthApplyError');
            }
		}

    }
}
