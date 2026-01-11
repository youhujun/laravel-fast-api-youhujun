<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-06-09 19:48:35
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-01 02:39:09
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAddressFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Admin\User\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Admin\CommonException;

use App\Events\Admin\CommonEvent;

use App\Service\Facade\Traits\V1\QueryService;

//用户地址
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAddress;

use App\Http\Resources\LaravelFastApi\V1\Admin\User\UserAddressCollection;

/**
 * @see  \App\Facade\Admin\User\User\AdminUserAddressFacade
 */
class AdminUserAddressFacadeService
{
   public function test()
   {
       echo "AdminUserAddressFacadeService test";
   }

   use QueryService;

   protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];


   /**
    * 添加用户地址
    *
    * @param [type] $validated
    * @param [type] $administrator
    * @return void
    */
   public function addUserAddress($validated,$administrator)
   {
        $result = code(config('admin_code.AddUserAddressError'));

		$defaultUser = User::find($validated['user_id']);
		//如果未设置联系人和手机号,就使用默认的用户和手机号
		if(!isset($validated['user_name']) || !$validated['user_name']){
			$validated['user_name'] = $defaultUser->nick_name?$defaultUser->nick_name:'保密';
		}
		if(!isset($validated['phone']) || !$validated['phone']){
			$validated['phone'] = $defaultUser->phone;
		}

        //先判断改地址是否是默认地址
        //是的话先将该用户的其他默认地址改为非默认
        if($validated['is_default'])
        {
            $defaultWhere = [];
            $defaultWhere[] = ['user_id','=',$validated['user_id']];
            $defaultWhere[] = ['is_default','=',1];
            //先检测有没有
            $defaultNumber = UserAddress::where($defaultWhere)->get()->count();

            if($defaultNumber)
            {
                $defaultUpdateData = [];
                $defaultUpdateData = [
                    'is_default'=>0
                ];

                $updateNoDefaultAddressResult = UserAddress::where($defaultWhere)->update($defaultUpdateData);

                if(!$updateNoDefaultAddressResult)
                {
                    throw new CommonException('AddUserAddressSetDefaultError');
                }
            }
        }

        $userAddress = new UserAddress;

        //p($validated);

        foreach($validated as $key =>$value)
        {
            if(isset($validated[$key]))
            {
                if(is_array($validated[$key]) )
                {
					if(count($validated[$key]) == 3){
						$userAddress->province_id = $validated['regionArray'][0];
						$userAddress->region_id = $validated['regionArray'][1];
						$userAddress->city_id = $validated['regionArray'][2];
						continue;
					}
                }
				else
				{
					$userAddress->$key = $value;
				}

                
            }
        }

        $userAddress->created_at = time();
        $userAddress->created_time = time();

        $userAddressResult = $userAddress->save();

        if(!$userAddressResult)
        {
            throw new CommonException('AddUserAddressError');
        }

        CommonEvent::dispatch($administrator,$validated,'AddUserAddress');

        $result = code(['code' => 0,'msg' => '添加用户地址成功!']);

        return $result;
   }

   /**
    * 获取用户地址
    *
    * @param [type] $validated
    * @param [type] $administrator
    * @return void
    */
   public function getUserAddress($validated,$administrator)
   {
       $result = code(config('admin_code.GetUserAddressError'));

       $this->setQueryOptions($validated);

       $where = [];

       $where[] = ['user_id','=',$validated['user_id']];

       $query = UserAddress::with(['province','region','city'])->where($where);

       if(isset($validated['sortType']))
        {
            $sortType = $validated['sortType'];

            $query->orderBy(self::$sort[$sortType][0],self::$sort[$sortType][1]);
        }

        $userAddressList = [];

        $userAddressList =  $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        $result = new  UserAddressCollection($userAddressList,['code' => 0,'msg' => '获取用户地址成功!']);

        return $result;
   }

   /**
    * 删除用户地址
    *
    * @param [type] $validated
    * @param [type] $administrator
    * @return void
    */
   public function deleteUserAddress($validated,$administrator)
   {
        $result = code(config('admin_code.DeleteUserAddressError'));

        $userAddress = UserAddress::find($validated['user_address_id']);

        if(!$userAddress)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $userAddress->deleted_at = date('Y-m-d H:i:s',time());
        $userAddress->deleted_time = time();

        $deleteUserAddressResult = $userAddress->save();

        if(!$deleteUserAddressResult)
        {
            throw new CommonException('DeleteUserAddressError');
        }

        CommonEvent::dispatch($administrator,$validated,'DeleteUserAddress');

        $result = code(['code' => 0,'msg' => '删除用户地址成功!']);

        return $result;
   }

    /**
    * 设置用户默认地址
    *
    * @param [type] $validated
    * @param [type] $administrator
    * @return void
    */
    public function setDefaultUserAddress($validated,$administrator)
    {
        $result = code(config('admin_code.SetDefaultUserAddressError'));

        $defaultWhere = [];
        $defaultWhere[] = ['user_id','=',$validated['user_id']];
        $defaultWhere[] = ['is_default','=',1];

        //先检测有没有其他默认地址 如果有需要先改为非默认
        $defaultNumber = UserAddress::where($defaultWhere)->get()->count();

        if($defaultNumber)
        {
            $defaultUpdateData = [];
            $defaultUpdateData = [
                'is_default' => 0
            ];

            $setNoDefaultAddressResult = UserAddress::where($defaultWhere)->update($defaultUpdateData);

            if(!$setNoDefaultAddressResult)
            {
                throw new CommonException('SetDefaultUserAddressOtherError');
            }
        }

        $userAddress = UserAddress::find($validated['user_address_id']);

        if(!$userAddress)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $revision = $userAddress->revision;

        $where = [];

        $where[] = ['id','=',$userAddress->id];
        $where[] = ['revision','=',$revision];

        $updateData = [
            'is_default' => 1,
            'revision' => $revision + 1,
            'updated_time' => time(),
            'updated_at' => date('Y-m-d H:i:s',time())
        ];

        $updateUserAddressResult = UserAddress::where($where)->update($updateData);

        if(!$updateUserAddressResult)
        {
            throw new CommonException('SetDefaultUserAddressError');
        }

        CommonEvent::dispatch($administrator,$validated,'SetDefaultUserAddress');

        $result = code(['code' => 0,'msg' => '设置用户默认地址成功!']);

        return $result;

    }
}
