<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-04 08:16:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-25 10:03:20
 * @FilePath: \app\Service\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacadeService.php
 */

namespace App\Service\Facade\LaravelFastApi\V1\Phone\User\User\Address;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use App\Service\Facade\Traits\V1\QueryService;

use App\Exceptions\Phone\CommonException;

use App\Events\Phone\CommonEvent;

use App\Models\LaravelFastApi\V1\User\Info\UserAddress;

use App\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserAddressResource;
use App\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserAddressCollection;

use App\Http\Resources\LaravelFastApi\V1\Phone\User\User\UserDefaultAddressResource;

/**
 * @see \App\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacade
 */
class PhoneUserAddressFacadeService
{
    public function test()
    {
       echo "PhoneUserAddressFacadeService test";
    }

    use QueryService;

    protected static $sort = [
      1 => ['created_time','asc'],
      2 => ['created_time','desc'],
    ];

    protected static $searchItem = [
        'comment'
    ];

    protected static $storage_public_path = DIRECTORY_SEPARATOR.'app'. DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;

   /**
     * 查询
     *
     * @param [type] $validated
     * @return void
     */
    public function getUserAddress($validated,$user)
    {

       $result = code(config('phone_code.GetUserAddressError'));

       $this->setQueryOptions($validated);

       $query = UserAddress::query();

       //是否删除
       if(isset($validated['is_delete']) && $validated['is_delete'])
       {
            $query->onlyTrashed();
       }


        if(isset($validated['timeRange']) && \count($validated['timeRange']) > 0)
        {
            if(isset($validated['timeRange'][0]) && !empty($validated['timeRange'][0]))
            {
                $this->whereBetween[] = [\strtotime($validated['timeRange'][0])];
            }

            if(isset($validated['timeRange'][1]) && !empty($validated['timeRange'][1]))
            {
                $this->whereBetween[] = [\strtotime($validated['timeRange'][1])];
            }

            if(isset($this->whereBetween) && count($this->whereBetween) > 0)
            {
                $query->whereBetween('created_time',$this->whereBetween);
            }

        }

        if(isset($validated['sortType']))
        {
            $sortType = $validated['sortType'];

            $query->orderBy(self::$sort[$sortType][0],self::$sort[$sortType][1]);
        }

        $download = null;

		$query->orderBy('is_top','asc');
		$query->orderBy('is_default','asc');
       
        $userAddressList = $query->paginate($this->perPage,$this->columns,$this->pageName,$this->page);

        if(\optional($userAddressList))
        {
			UserAddressResource::setAtuoShowRegion(20);
			
            $result = new UserAddressCollection($userAddressList,['code'=>0,'msg'=>'获取用户地址成功!'],$download);
            //如果要增加其他返回参数,需要在UserAddressCollection处理
        }

        return  $result;
    }

    /**
     * 添加
     *
     * @param [type] $validated
     * @param [type] $user
     * @return void
     */
    public function addUserAddress($validated,$user)
    {
        $result = code(config('phone_code.AddUserAddressError'));

        $userAddressObject = new UserAddress;

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        foreach ( $validated as $key => $value)
        {
			if($key == 'region_id_array')
			{
				continue;
			}
			
            if(isset($value))
            {
                $userAddressObject->$key = $value;
            }
        }

		$userAddressObject->user_id = $user->id;

		$userAddressObject->province_id = $validated['region_id_array'][0];
		$userAddressObject->region_id = $validated['region_id_array'][1];
		$userAddressObject->city_id = $validated['region_id_array'][2];

        $userAddressObject->created_time = time();
        $userAddressObject->created_at = time();

        $addResult = $userAddressObject->save();

        if(!$addResult)
        {
             throw new CommonException('AddUserAddressError');
        }

        CommonEvent::dispatch($user,$validated,'AddUserAddress');

        $result = code(['code'=>0,'msg'=>'添加用户地址成功']);

        return $result;
    }


    /**
     * 更新
     *
     * @param [type] $validated
     * @param [type] $user
     * @return void
     */
    public function updateUserAddress($validated,$user)
    {
        $result = code(config('phone_code.UpdateUserAddressError'));

        if(!count($validated))
        {
            throw new CommonException('ParamsIsNullError');
        }

        $userAddressObject = UserAddress::find($validated['id']);

        if(!$userAddressObject)
        {
            throw new CommonException('ThisDataNotExistsError');
        }

        $where = [];

        $updateData = [];

        $where[] = ['revision','=',$userAddressObject ->revision];
		$where[] = ['user_id','=',$user->id];

        foreach ( $validated as $key => $value)
        {
            if($key === 'id')
            {
                $where[] = ['id','=',$value];
                continue;
            }

            if($key == 'region_id_array')
			{
				continue;
			}

            if(isset($value))
            {
                $updateData[$key] = $value;
            }
        }

        $updateData['revision'] = $userAddressObject ->revision + 1;

        $updateData['updated_time'] = time();

        $updateData['updated_at']  = date('Y-m-d H:i:s',time());

		$updateData['province_id'] = $validated['region_id_array'][0];
		$updateData['region_id'] = $validated['region_id_array'][1];
		$updateData['city_id'] = $validated['region_id_array'][2];

        $updateResult = UserAddress::where($where)->update($updateData);

        if(!$updateResult)
        {
           throw new CommonException('UpdateUserAddressError');
        }

        CommonEvent::dispatch($user,$validated,'UpdateUserAddress');

        $result = code(['code'=>0,'msg'=>'更新用户地址成功']);

        return $result;
    }


    /**
     * 删除
     *
     * @param [type] $id
     * @param [type] $user
     * @return void
     */
    public function deleteUserAddress($validated,$user)
    {
        //删除
        $result = code(config('phone_code.UnSetDefaultUserAddressError'));

        $eventName = 'UnSetDefaultUserAddress';

        if($validated['is_delete'])
        {
            $result = code(config('phone_code.DeleteUserAddressError'));

            $eventName = 'DeleteUserAddress';
        }

        if($validated['is_delete'])
        {
            $userAddressObject = UserAddress::where('id',$validated['id'])->where('user_id','=',$user->id)->first();

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $userAddressObjectResult =  $userAddressObject->delete();
        }
        else
        {
            //恢复
            $userAddressObject = UserAddress::withTrashed()->find($validated['id']);

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

            $userAddressObjectResult =  $userAddressObject->restore();

        }

        if(!$userAddressObjectResult )
        {
            if($validated['is_delete'])
            {
                 throw new CommonException('DeleteUserAddressError');
            }

            throw new CommonException('UnSetDefaultUserAddressError');

        }

        CommonEvent::dispatch($user,$validated,$eventName);

        $result = code(['code'=>0,'msg'=>'恢复用户地址成功']);

        if($validated['is_delete'])
        {
             $result = code(['code'=>0,'msg'=>'删除用户地址成功']);
        }

        return $result;
    }

	 /**
     * 设为默认地址
     *
     * @param [type] $id
     * @param [type] $user
     * @return void
     */
    public function setDefaultUserAddress($validated,$user)
    {
        
        $result = code(config('phone_code.UnSetDefaultUserAddressError'));

        $eventName = 'UnSetDefaultUserAddress';

        if($validated['is_default'])
        {
            $result = code(config('phone_code.SetDefaultUserAddressError'));

            $eventName = 'SetDefaultUserAddress';
        }

        if($validated['is_default'])
        {

			$where = [];
			$where[] = ['is_default','=',1];
			$where[] = ['user_id','=',$user->id];
			$updateData = [];
			$updateData['is_default'] = 0;
			$updateOtherResult = UserAddress::where($where)->update($updateData);

            $userAddressObject = UserAddress::find($validated['id']);

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

			//先将其他的默认改为非默认
            $userAddressObject->is_default = 1;

			$updateResult = $userAddressObject->save();
        }
        else
        {
             $userAddressObject = UserAddress::find($validated['id']);

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

			//先将其他的默认改为非默认
            $userAddressObject->is_default = 0;

			$updateResult = $userAddressObject->save();

        }

        if(!$updateResult )
        {
            if($validated['is_default'])
            {
                throw new CommonException('SetDefaultUserAddressError');
            }

            throw new CommonException('UnSetDefaultUserAddressError');

        }

        CommonEvent::dispatch($user,$validated,$eventName);

        $result = code(['code'=>0,'msg'=>'取消默认用户地址成功']);

        if($validated['is_default'])
        {
            $result = code(['code'=>0,'msg'=>'设置默认用户地址成功']);
        }

        return $result;
    }

	/**
     * 设为置顶
     *
     * @param [type] $id
     * @param [type] $user
     * @return void
     */
    public function setTopUserAddress($validated,$user)
    {
        
        $result = code(config('phone_code.UnSetTopUserAddressError'));

        $eventName = 'UnSetTopUserAddress';

        if($validated['is_top'])
        {
            $result = code(config('phone_code.SetTopUserAddressError'));

            $eventName = 'SetTopUserAddress';
        }

        if($validated['is_top'])
        {
		
            $userAddressObject = UserAddress::where('id',$validated['id'])->where('user_id',$user->id)->first();

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

			//先将其他的默认改为非默认
            $userAddressObject->is_top = 1;

			$updateResult = $userAddressObject->save();
        }
        else
        {
             $userAddressObject = UserAddress::find($validated['id']);

            if(!$userAddressObject)
            {
                throw new CommonException('ThisDataNotExistsError');
            }

			//先将其他的默认改为非默认
            $userAddressObject->is_top = 0;

			$updateResult = $userAddressObject->save();

        }

        if(!$updateResult )
        {
            if($validated['is_top'])
            {
                throw new CommonException('SetTopUserAddressError');
            }

            throw new CommonException('UnSetTopUserAddressError');

        }

        CommonEvent::dispatch($user,$validated,$eventName);

        $result = code(['code'=>0,'msg'=>'取消置顶用户地址成功']);

        if($validated['is_top'])
        {
            $result = code(['code'=>0,'msg'=>'置顶用户地址成功']);
        }

        return $result;
    }

	/**
	 * 获取用户默认地址
	 *
	 * @param  [type] $validated
	 * @param  [type] $user
	 */
	public function getUserDefaultAddress($validated,$user)
	{
		$result = code(config('phone_codeGetUserDefaultAddressError'));

		$userDefaultAddress = UserAddress::where('user_id',$user->id)->where('is_default',1)->first();

		//p($userDefaultAddress);die;

		$result = ['code'=>0,'msg'=>'获取用户默认地址成功!','data'=>null];

		if($userDefaultAddress)
		{
			
			$result['data'] =  new UserDefaultAddressResource($userDefaultAddress);
		}
		
		return $result;
	}
}
