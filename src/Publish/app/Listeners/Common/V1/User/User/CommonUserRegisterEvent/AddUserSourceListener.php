<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-21 16:40:16
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-22 12:04:24
 * @FilePath: \app\Listeners\Common\V1\User\User\CommonUserRegisterEvent\AddUserSourceListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\Common\V1\User\User\CommonUserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Exceptions\Common\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
use App\Models\LaravelFastApi\V1\User\User;

use App\Facades\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade;

/**
 * @see \App\Events\Common\V1\User\User\CommonUserRegisterEvent
 */
class AddUserSourceListener
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

        $where = [];

         //查找上级用户的id
		//邀请的id
        if(isset($validated['inviteId']) && !empty($validated['inviteId']))
        {
            $isToSource = 1;
            $where[] = ['userId','=',$validated['inviteId']];
        }

		//邀请码
        if(isset($validated['inviteCode']) && !empty($validated['inviteCode']))
        {
            $isToSource = 1;
            $where[] = ['invite_code','=',$validated['inviteCode']];
        }

	
		//如果有手机推广条件
		if(count($where)){

			$sourceUser = User::where($where)->first();
			if(!$sourceUser)
			{
				if($isTransation)
				{
					DB::rollBack();
				}

				throw new CommonException('InviteSourceUserNotExistsError');
			}

			$user->source_user_id = $sourceUser->id;
			$user->source_userId = $sourceUser->userId;

			//保存用户上级
			$userResult = $user->save();

			if(!$userResult)
			{
				if($isTransation)
				{
					DB::rollBack();
				}

				throw new CommonException('SaveUserSourceError');
			}

			$userSourceUnionData = PhoneUserSourceFacade::getInsertUserSourceUnionData($user);

			//p($userSourceUnionData);die;

			$userSourceUnionData['user_id'] = $user->id;
			$userSourceUnionData['created_at'] = date('Y-m-d H:i:s',time());
			$userSourceUnionData['created_time'] = time();


			$serSourceUnionResult =  UserSourceUnion::insert($userSourceUnionData);

			if(!$serSourceUnionResult)
			{
				if($isTransation)
				{
					DB::rollBack();
				}

				throw new CommonException('AddUserSourceUnionError');
			}
		}
		
		
        
    }
}
