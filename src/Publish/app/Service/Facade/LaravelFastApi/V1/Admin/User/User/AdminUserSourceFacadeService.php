<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-08-06 09:24:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-06 09:32:55
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserSourceFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Models\LaravelFastApi\V1\User\User;

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserSourceFacade
 */
class AdminUserSourceFacadeService
{
   public function test()
   {
       echo "AdminUserSourceFacadeService test";
   }

    protected $userUnionProperty = ['first_id','second_id'];

	/**
    * 获取需要添加用户的UserSourceUnion的id数组
    *
    * @param  User    $user
    * @param  integer $number
    */
   public function getInsertUserSourceUnionId(User $user,$number = 2)
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
                $userSourceId = $sourceUser->source_id;
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
   public function getInsertUserSourceUnionData(User $user,$number = 2)
   {
       $userSourceUnionIdArray =  $this-> getInsertUserSourceUnionId($user,$number);

       $UserSourceUnionData = [];

       foreach($userSourceUnionIdArray as $key => $value)
       {
            $UserSourceUnionData[$this->userUnionProperty[$key]] = $value;
       }

       return $UserSourceUnionData;
   }


}
