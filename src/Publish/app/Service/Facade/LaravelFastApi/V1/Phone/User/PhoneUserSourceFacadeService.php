<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-03 09:46:04
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-02 21:19:06
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Facade\LaravelFastApi\V1\Phone\User\PhoneUserSourceFacade
 */
class PhoneUserSourceFacadeService
{
   public function test()
   {
       echo "PhoneUserSourceFacadeService test";
   }

   protected $userUnionProperty = ['first_id','second_id'];

   /**
    * 获取需要添加用户的UserSourceUnion的id数组
    *
    * @param  User    $user
    * @param  integer $number 表示几级分销
    */
   public function getInsertUserSourceUnionId(User $user,$number)
   {
       $userSourceUnionIdArray = [];

       $sourceUser = null;

       $userSourceId = 0;

       for ($i=0; $i <$number ; $i++)
       {
            if($i === 0)
            {
                $sourceUser = $user;
            }
            else
            {
                if($userSourceId)
                {
                    $sourceUser = User::where('id',$userSourceId)->where('switch',1)->first();
                }

            }

            if($sourceUser)
            {
                $userSourceId = $sourceUser->source_user_id;
            }
            else
            {
                $userSourceId = 0;
            }

            $userSourceUnionIdArray[] = $userSourceId;

       }

       return $userSourceUnionIdArray;
   }

   /**
    * 获取需要添加的关联数据
    *
    * @param  User    $user
    * @param  integer $number
    */
   public function getInsertUserSourceUnionData(User $user)
   {
	   $number = count($this->userUnionProperty);
	   
	   //获取需要插入的id
       $userSourceUnionIdArray =  $this-> getInsertUserSourceUnionId($user,$number);

       $UserSourceUnionData = [];

       foreach($userSourceUnionIdArray as $key => $value)
       {
            $UserSourceUnionData[$this->userUnionProperty[$key]] = $value;
       }

       return $UserSourceUnionData;
   }
}
