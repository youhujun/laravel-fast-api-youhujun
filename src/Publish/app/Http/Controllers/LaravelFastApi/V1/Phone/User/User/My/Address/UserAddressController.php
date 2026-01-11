<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-09-04 08:15:28
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:59:40
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\Address\UserAddressController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\Address;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Rules\Pub\Required;
use App\Rules\Pub\Numeric;
use App\Rules\Pub\CheckString;
use App\Rules\Pub\CheckBetween;
use App\Rules\Pub\CheckArray;
use App\Rules\Pub\FormatTime;
use App\Rules\Pub\CheckUnique;
use App\Rules\Pub\ChineseCodeNumberLine;
use App\Exceptions\Common\RuleException;
use App\Rules\Common\Phone;

use App\Facade\LaravelFastApi\V1\Phone\User\User\Address\PhoneUserAddressFacade;

class UserAddressController extends Controller
{
	
     /**
     * 查询
     */
    public function getUserAddress(Request $request)
    {
        $result = code(\config('phone_code.PhoneAuthError'));

        $user = Auth::guard('phone_token')->user();

        if(Gate::forUser($user)->allows('phone-user-role'))
        {

             $validator = Validator::make(
                $request->all(),
                [
                   
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['sortType'])) {
                throw new RuleException('RuleRequiredError', 'sortType');
            }

			//p($validated);die;

			if(count($validated))
			{
				$result = PhoneUserAddressFacade::getUserAddress(f($validated),$user);
			}

            
        }

        return $result;
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addUserAddress(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if(Gate::forUser($user)->allows('phone-user-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
					'address_type'=>['bail',new Required,new Numeric],
					'user_name'=>['bail',new Required,new CheckString],
					'phone'=>['bail',new Required,new Phone],
					'region_id_array'=>['bail',new Required,new CheckArray],
					'address_info'=>['bail',new Required,new CheckString]
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['address_type'])) {
                throw new RuleException('RuleRequiredError', 'address_type');
            }

            if (!isset($validated['user_name'])) {
                throw new RuleException('RuleRequiredError', 'user_name');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            if (!isset($validated['region_id_array'])) {
                throw new RuleException('RuleRequiredError', 'region_id_array');
            }

            if (!isset($validated['address_info'])) {
                throw new RuleException('RuleRequiredError', 'address_info');
            }

			// p($validated);die;

            if(count($validated))
			{
				 $result = PhoneUserAddressFacade::addUserAddress(f($validated),$user);
			}
        }

        return $result;
    }

    /**
     * 修改
     *
     * @param Request $request
     * @return void
     */
    public function updateUserAddress(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        $id = checkId($request->input('id'));

        if(Gate::forUser($user)->allows('phone-user-role'))
        {
           $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                   	'address_type'=>['bail',new Required,new Numeric],
					'user_name'=>['bail',new Required,new CheckString],
					'phone'=>['bail',new Required,new Phone],
					'region_id_array'=>['bail',new Required,new CheckArray],
					'address_info'=>['bail',new Required,new CheckString]
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['address_type'])) {
                throw new RuleException('RuleRequiredError', 'address_type');
            }

            if (!isset($validated['user_name'])) {
                throw new RuleException('RuleRequiredError', 'user_name');
            }

            if (!isset($validated['phone'])) {
                throw new RuleException('RuleRequiredError', 'phone');
            }

            if (!isset($validated['region_id_array'])) {
                throw new RuleException('RuleRequiredError', 'region_id_array');
            }

            if (!isset($validated['address_info'])) {
                throw new RuleException('RuleRequiredError', 'address_info');
            }

			//p($validated);die;

		    if(count($validated))
			{
				$result = PhoneUserAddressFacade::updateUserAddress(f($validated),$user);
			}

           
        }

        return $result;
    }


    /**
     * 删除
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserAddress(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if(Gate::forUser($user)->allows('phone-user-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_delete'=>['bail',new Required,new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['is_default'])) {
                throw new RuleException('RuleRequiredError', 'is_default');
            }

			if(count($validated))
			{
				$result = PhoneUserAddressFacade::deleteUserAddress(f($validated),$user);
			}
        }

        return $result;
    }

	/**
     * 设置用户默认地址
     *
     * @param Request $request
     * @return void
     */
    public function setDefaultUserAddress(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if(Gate::forUser($user)->allows('phone-user-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_default'=>['bail',new Required,new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['is_default'])) {
                throw new RuleException('RuleRequiredError', 'is_default');
            }

			if(count($validated))
			{
				$result = PhoneUserAddressFacade::setDefaultUserAddress(f($validated),$user);
			}
        }

        return $result;
    }

	/**
     * 置顶用户地址
     *
     * @param Request $request
     * @return void
     */
    public function setTopUserAddress(Request $request)
    {
        $user = Auth::guard('phone_token')->user();

        $result = code(\config('phone_code.PhoneAuthError'));

        if(Gate::forUser($user)->allows('phone-user-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                    'id'=>['bail',new Required,new Numeric],
                    'is_top'=>['bail',new Required,new Numeric],
                ],
                [ ]
            );

            $validated = $validator->validated();

            if (!isset($validated['id'])) {
                throw new RuleException('RuleRequiredError', 'id');
            }

            if (!isset($validated['is_default'])) {
                throw new RuleException('RuleRequiredError', 'is_default');
            }

			if(count($validated))
			{
				$result = PhoneUserAddressFacade::setTopUserAddress(f($validated),$user);
			}
        }

        return $result;
    }

	/**
	 * 获取用户默认地址
	 *
	 * @param  Request $request
	 */
	public function getUserDefaultAddress(Request $request)
	{
		$user = Auth::guard('phone_token')->user();
		
	 if(Gate::forUser($user)->allows('phone-user-role'))
        {

            $validator = Validator::make(
                $request->all(),
                [
                ],
                [ ]
            );

            $validated = $validator->validated();

			// p($user);die;

			$result = PhoneUserAddressFacade::getUserDefaultAddress(f($validated),$user);
			
        }

        return $result;
	}
	
}
