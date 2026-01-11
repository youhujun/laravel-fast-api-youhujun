<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-15 17:42:17
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent\AddUserRealAuthApplyListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\DB;
use App\Exceptions\Admin\CommonException;

use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;

/**
 * @see \App\Events\LaravelFastApi\V1\Admin\User\User\SetUserIdCardEvent
 */
class AddUserRealAuthApplyListener
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

        //添加完用户银行卡以后,要添加用户实名认证申请
        //先查询该用户是否有申请中或者审核通过的情况
        $where = [];
        $where[] = ['user_id','=',$validated['user_id']];
        //正在申请中
        $where[] = ['status','=',10];

        $user = User::find($validated['user_id']);

        if(!$user)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $userApplyRealAuthNumber =  UserRealAuthLog::where($where)->get()->count();

        if($userApplyRealAuthNumber)
        {
            throw new CommonException('ThisUserHasRealAuthError');
        }


        $userApplyRealAuth = new UserRealAuthLog();

        $userApplyRealAuth->created_at = time();
        $userApplyRealAuth->created_time = time();
        $userApplyRealAuth->auth_apply_at = time();
        $userApplyRealAuth->auth_apply_time = time();
        $userApplyRealAuth->status = 10;
        $userApplyRealAuth->user_id = $validated['user_id'];

        $userApplyRealAuthResult = $userApplyRealAuth->save();

        if(!$userApplyRealAuthResult)
        {
            throw new CommonException('SetUserRealAuthLogError');
        }
    }
}
