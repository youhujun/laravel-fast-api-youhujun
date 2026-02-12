<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-05 22:37:32
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserDetailsFacadeService.php
 */

namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use YouHuJun\Tool\App\Facades\V1\Calendar\CalendarFacade;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Services\Facade\Traits\V1\QueryService;

//模型
//用户
use App\Models\LaravelFastApi\V1\User\User;
//用户地址
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;
//用户详情
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
//用户银行卡
use App\Models\LaravelFastApi\V1\User\Info\UserBank;

use App\Models\LaravelFastApi\V1\User\Info\UserIdCard;
//用户申请实名认证表
use App\Models\LaravelFastApi\V1\User\Log\UserRealAuthLog;
//用户父关联表
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
//用户二维码表
use App\Models\LaravelFastApi\V1\User\Info\UserQrcode;

//生成用户二维码
use App\Events\LaravelFastApi\V1\Admin\User\User\MakeUserQrcodeEvent;

//响应资源
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserResource;
use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserCollection;
use App\Http\Resources\LaravelFastApi\V1\Admin\System\Picture\AlbumPictureResource;
use LDAP\Result;

/**
 * @see \App\Facades\Admin\User\User\AdminUserDetailsFacade
 */
class AdminUserDetailsFacadeService
{
   public function test()
   {
       echo "AdminUserDetailsFacadeService test";
   }

   use QueryService;

   protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];


   /**
    * 修改用户手机号
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
   public function updateUserPhone($validated,$admin)
   {
        $result = code(config('admin_code.UpdateUserPhoneError'));

        $user = User::find($validated['user_id']);

        $where = [];
        $updateData = [];

        $revision = $user->revision;

        $where[] = ['id','=',$user->id];
        $where[] = ['revision','=',$revision];

        $updateData = [
            'revision'=>$revision + 1,
            'phone'=>$validated['phone'],
            'updated_at'=>date('Y-m-d H:i:s',time()),
            'updated_time'=>time(),
        ];

        $updatePhoneResult = User::where($where)->update($updateData);

        if(!$updatePhoneResult)
        {
            throw new CommonException('UpdateUserPhoneError');
        }

        CommonEvent::dispatch($admin,$validated,'UpdateUserPhone');

        $result = code(['code' => 0,'msg' => '更新用户手机号成功']);

        return $result;
   }



   /**
    * 修改用户真实姓名
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
   public function updateUserRealName($validated,$admin)
   {
        $result = code(config('admin_code.UpdateUserRealNameError'));

        $userInfo = UserInfo::where('user_id',$validated['user_id'])->first();

        $where = [];
        $updateData = [];

        $revision = $userInfo->revision;

        $where[] = ['id','=',$userInfo->id];
        $where[] = ['revision','=',$revision];

        $updateData = [
            'revision'=>$revision + 1,
            'real_name'=>$validated['real_name'],
            'updated_at'=>date('Y-m-d H:i:s',time()),
            'updated_time'=>time(),
        ];

        $updateRealNameResult = UserInfo::where($where)->update($updateData);

        if(!$updateRealNameResult)
        {
            throw new CommonException('UpdateUserRealNameError');
        }

        CommonEvent::dispatch($admin,$validated,'UpdateUserRealName');

        $result = code(['code' => 0,'msg' => '更新用户姓名成功']);

        return $result;
   }


   /**
    * 修改用户昵称
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
    public function updateUserNickName($validated,$admin)
    {
         $result = code(config('admin_code.UpdateUserNickNameError'));

         $userInfo = UserInfo::find($validated['user_id']);
         $where = [];
         $updateData = [];

         $revision = $userInfo->revision;

         $where[] = ['id','=',$userInfo->id];
         $where[] = ['revision','=',$revision];

         $updateData = [
             'revision'=>$revision + 1,
             'nick_name'=>$validated['nick_name'],
             'updated_at'=>date('Y-m-d H:i:s',time()),
             'updated_time'=>time(),
         ];

         $updateNickNameResult = UserInfo::where($where)->update($updateData);

         if(!$updateNickNameResult)
         {
             throw new CommonException('UpdateUserNickNameError');
         }

         CommonEvent::dispatch($admin,$validated,'UpdateUserNickName');

         $result = code(['code' => 0,'msg' => '更新用户昵称成功']);

         return $result;
    }




   /**
    * 修改用户性别
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
    public function updateUserSex($validated,$admin)
    {
         $result = code(config('admin_code.UpdateUserSexError'));

         $userInfo = UserInfo::find($validated['user_id']);

         $where = [];
         $updateData = [];

         $revision = $userInfo->revision;

         $where[] = ['id','=',$userInfo->id];
         $where[] = ['revision','=',$revision];

         $updateData = [
             'revision'=>$revision + 1,
             'sex'=>$validated['sex'],
             'updated_at'=>date('Y-m-d H:i:s',time()),
             'updated_time'=>time(),
         ];

         $updateSexResult = UserInfo::where($where)->update($updateData);

         if(!$updateSexResult)
         {
            throw new CommonException('UpdateUserSexError');
         }

         CommonEvent::dispatch($admin,$validated,'UpdateUserSex');

         $result = code(['code' => 0,'msg' => '更新用户性别成功']);

         return $result;
    }

     /**
     * 更改用户级别
     *
     * @param [type] $validated
     * @param [type] $admin
     * @return void
     */
    public function changeUserLevel($validated,$admin)
    {
        $result = code(config('admin_code.ChangeUserLevelError'));

        $user = User::find($validated['user_id']);

        $revision = $user->revision;

        $updateData = ['level_id'=>$validated['level_id'],'updated_time'=>time(),'updated_at'=>\date('Y-m-d H:i:s',time()),'revision'=>$revision + 1];

        $userResult = User::where('id',$validated['user_id'])->where('revision',$revision)->update($updateData);

        if(!$userResult)
        {
            throw new CommonException('ChangeUserLevelError');
        }

        CommonEvent::dispatch($admin,$validated,'ChangeUserLevel');

        $result = code(['code' => 0,'msg' => '更新用户级别成功']);

        return $result;
    }

    /**
    * 修改用户性别
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
    public function updateUserBirthdayTime($validated,$admin)
    {
         $result = code(config('admin_code.UpdateUserBirthdayTimeError'));

         $userInfo = UserInfo::find($validated['user_id']);

         $where = [];
         $updateData = [];

         $revision = $userInfo->revision;

         $where[] = ['id','=',$userInfo->id];
         $where[] = ['revision','=',$revision];

        if(isset($validated['solar_birthday_time']))
        {
            $lunar = CalendarFacade::solarToLunarString($validated['solar_birthday_time']);
        }

         $updateData = [
             'revision'=>$revision + 1,
             'solar_birthday_at'=>$validated['solar_birthday_time'],
             'solar_birthday_time'=>$validated['solar_birthday_time']?\strtotime($validated['solar_birthday_time']):0,
             'chinese_birthday_at'=>$lunar,
             'chinese_birthday_time'=> \strtotime($lunar),
             'updated_at'=>date('Y-m-d H:i:s',time()),
             'updated_time'=>time(),
         ];

         $updateSexResult = UserInfo::where($where)->update($updateData);

         if(!$updateSexResult)
         {
             throw new CommonException('UpdateUserBirthdayTimeError');
         }

         CommonEvent::dispatch($admin,$validated,'UpdateUserBirthdayTime');

         $result = code(['code' => 0,'msg' => '更新用户出生日期成功']);

         return $result;
    }

    /**
    * 重置用户密码
    *
    * @param [type] $validated
    * @param [type] $admin
    * @return void
    */
    public function resetUserPassword($validated,$admin)
    {
        $result = code(config('admin_code.ResetUserPasswordError'));

        $user = User::find($validated['user_id']);

        if(!$user)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];
        $updateData = [];

        $revision = $user->revision;

        $where[] = ['id','=',$user->id];
        $where[] = ['revision','=',$revision];

        $updateData = [
            'revision' => $revision + 1,
            'password' => Hash::make($validated['password']),
            'updated_at' => \showTime(time(),true),
            'updated_time' => time(),
        ];

        $updateResult = User::where($where)->update($updateData);

        if(!$updateResult)
        {
        throw new CommonException('ResetUserPasswordError');
        }

        CommonEvent::dispatch($admin,$validated,'ResetUserPassword');

        $result = code(['code' => 0,'msg' => '重置用户密码成功']);

        return $result;
    }


    /**
     * 获取用户二维码
     */
    public function getUserQrcode($validated,$admin)
    {
        $result = code(config('admin_code.GetUserQrcodeError'));

        $where = [];
        $where[] = ['user_id','=',$validated['user_id']];
        $where[] = ['is_default','=',1];

        $userQrcode = UserQrcode::with(['albumPicture'])->where($where)->first();

        $data = [];

        if($userQrcode)
        {
            $userOrcodeArrayObject = $userQrcode->getRelations('ablbumPicture');

            $userQrcodePictureObject = $userOrcodeArrayObject['albumPicture'];

            $data['data'] = new AlbumPictureResource($userQrcodePictureObject);
        }

        $result = code(['code'=> 0 ,'msg'=>'获取用户二维码成功'],$data);

        return $result;
    }

	/**
	 * 生成用户二维码
	 */
	public function makeUserQrcode($validated,$admin)
	{
		$result = code(config('admin_code.MakeUserQrcodeError'));
		
		$user = User::find($validated['user_id']);

		MakeUserQrcodeEvent::dispatch($user,$admin,$validated);

		CommonEvent::dispatch($admin,$validated,'MakeUserQrcode');

        $result = code(['code' => 0,'msg' => '用户二维码生成成功']);

		return $result;
	}
}
