<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-08 14:10:45
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 14:58:03
 * @FilePath: \App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacadeService.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

/**
 * @see \App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade
 */
namespace App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Models\LaravelFastApi\V1\Admin\Admin;

class AdminPersonalFacadeService
{
   public function test()
   {
       echo "AdminPersonalFacadeService test";
   }

   /**
	* 确认修改头像
    */
   public function updateAvatar($validated,$admin)
   {
		Redis::hdel("admin_info:admin_info",$admin->id);

        $result = code(['code'=>0,'msg'=>'修改头像成功!']);

        return $result;
   }

   /**
	* 修改手机号
    */
   public function updatePhone($validated,$admin)
   {
		$result = ['code'=>10000,'msg'=>'修改手机号失败'];

		$adminObject = Admin::find($admin->id);

		$where = [];
		$where[] = ['revision','=',$adminObject ->revision];
		$where[] = ['id','=',$adminObject ->id];

		$udpateData = [
			'phone'=>$validated['phone']
		];

		$updateData['revision'] = $adminObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $updateResult = Admin::where($where)->update($updateData);

        if(!$updateResult)
        {
           throw new CommonException('UpdateAdminPhoneError');
        }

        CommonEvent::dispatch($admin,$validated,'UpdateAdminPhone');

        $result = code(['code'=>0,'msg'=>'修改手机号成功']);

        return $result;

   }

   /**
	* 修改密码
	*
	* @param  [type] $validated
	* @param  [type] $admin
	*/
   public function updatePassword($validated,$admin)
   {
		$result = ['code'=>10000,'msg'=>'修改密码失败'];

		$adminObject = Admin::find($admin->id);

		$where = [];
		$where[] = ['revision','=',$adminObject ->revision];
		$where[] = ['id','=',$adminObject ->id];

		['password'=>$password,'repass'=>$repass] = $validated;

		if($password !== $repass){
			throw new CommonException('AdminPasswordNotSameError');
		}

		$udpateData = [
			'password'=>Hash::make($validated['password'])
		];

		$updateData['revision'] = $adminObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

        $updateResult = Admin::where($where)->update($updateData);

        if(!$updateResult)
        {
           throw new CommonException('UpdateAdminPasswordError');
        }

        CommonEvent::dispatch($admin,$validated,'UpdateAdminPassword');

        $result = code(['code'=>0,'msg'=>'修改手机号成功']);

        return $result;
   }
}
