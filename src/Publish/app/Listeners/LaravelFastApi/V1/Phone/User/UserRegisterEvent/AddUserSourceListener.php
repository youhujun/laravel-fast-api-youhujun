<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 16:57:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 12:03:55
 * @FilePath: \app\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent\AddUserSourceListener.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Listeners\LaravelFastApi\V1\Phone\User\UserRegisterEvent;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Exceptions\Phone\CommonException;

use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
use App\Models\LaravelFastApi\V1\User\User;

use App\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade;

/**
 * @see \App\Events\LaravelFastApi\V1\Phone\User\UserRegisterEvent
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

        //p($validated);die;

        //是否推荐
        $isToSource = 0;
        $where = [];

         //查找上级用户的id
        if(isset($validated['inviteId']) && !empty($validated['inviteId']))
        {
            $isToSource = 1;
            $where[] = ['id','=',$validated['inviteId']];
        }

        if(isset($validated['inviteCode']) && !empty($validated['inviteCode']))
        {
            $isToSource = 1;
            $where[] = ['invite_code','=',$validated['inviteCode']];
        }

        if($isToSource)
        {
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

			}
            
            $userSourceUnionData = PhoneUserSourceFacade::getInsertUserSourceUnionData($user);

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
