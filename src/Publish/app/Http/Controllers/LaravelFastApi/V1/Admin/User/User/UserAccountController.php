<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 17:22:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-03 17:21:33
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAccountController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\User;

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

use App\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserAccountFacade;

class UserAccountController extends Controller
{

    /**
     * 操作用户账户
     *
     * @param Request $request
     * @return void
     */
    public function setUserAccount(Request $request )
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {

             $validator = Validator::make(
                $request->all(),
                [
                    'find'=>['bail','nullable',new CheckString],
                    'user_id'=> ['bail',new Required,new Numeric],
                    'account_type'=> ['bail',new Required,new Numeric],
                    'action_type'=> ['bail',new Required,new Numeric],
                    'amount'=> ['bail',new Required,new Numeric]
                ],
                []
            );

            $validated = $validator->validated();

            if (!isset($validated['user_id'])) {
                throw new RuleException('RuleRequiredError', 'user_id');
            }

            if (!isset($validated['account_type'])) {
                throw new RuleException('RuleRequiredError', 'account_type');
            }

            if (!isset($validated['action_type'])) {
                throw new RuleException('RuleRequiredError', 'action_type');
            }

            if (!isset($validated['amount'])) {
                throw new RuleException('RuleRequiredError', 'amount');
            }

            //p($validated);die;

            $result = AdminUserAccountFacade::setUserAccount(f($validated),$user);
        }

        return $result;
    }


    /**
     * 获取用户账户日志
     *
     * @param Request $request
     * @return void
     */
    public function getUserAccountLog(Request $request )
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {

             $validator = Validator::make(
                $request->all(),
                [
                    'timeRange'=>['bail','nullable',new CheckArray],
                    'currentPage'=>['bail','nullable',new Numeric],
                    'pageSize'=>['bail','nullable',new Numeric],
                    'sortType'=>['bail',new Required,new Numeric],
                    'user_id'=> ['bail',new Required,new Numeric],
                    'account_type'=> ['bail',new Required,new Numeric],
                    'action_type'=> ['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

             //p($validated);die;

            $result = AdminUserAccountFacade::getUserAccountLog(f($validated),$user);
        }

        return $result;
    }

	/**
	 * 获取用户账户信息
	 *
	 * @param  Request $request
	 */
	public function getUserAccountInfo(Request $request)
	{
		$result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if(Gate::forUser($user)->allows('admin-role'))
        {

             $validator = Validator::make(
                $request->all(),
                [
                    'user_id'=> ['bail',new Required,new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

			if(!isset($validated['user_id'])){
				throw new RuleException('RuleRequiredError','user_id');
			}

             //p($validated);die;

            $result = AdminUserAccountFacade::getUserAccountInfo(f($validated),$user);
        }

        return $result;
	}

}
