<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-09-11 15:32:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-25 17:18:23
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserBankController.php
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

use App\Facades\LaravelFastApi\V1\Admin\User\User\AdminUserBankFacade;

class UserBankController extends Controller
{
    /**
     * 添加银行卡
     *
     * @param Request $request
     * @return void
     */
    public function addUserBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role'))
        {
            $validator = Validator::make(
                $request->all(),
               [
                    'user_id' => ['bail', new Required, new Numeric],
                    'bank_id' => ['bail', new Required, new Numeric],
                    'bank_number' => ['bail', new Required, new Numeric, new CheckUnique('user_bank', 'bank_number')],
                    'bank_account' => ['bail', new Required, new CheckString],
                    'bank_address' => ['bail', 'nullable', new CheckString],
                    'bank_front_id' => ['bail', 'nullable', new Numeric],
                    'bank_back_id' => ['bail', 'nullable', new Numeric],
                    'is_default' => ['bail', 'nullable', new Numeric],
                    'sort' => ['bail', 'nullable', new Numeric],
               ],
               []
           );

          $validated = $validator->validated();

          if (!isset($validated['user_id'])) {
              throw new RuleException('RuleRequiredError', 'user_id');
          }

          if (!isset($validated['bank_id'])) {
              throw new RuleException('RuleRequiredError', 'bank_id');
          }

          if (!isset($validated['bank_number'])) {
              throw new RuleException('RuleRequiredError', 'bank_number');
          }

          if (!isset($validated['bank_account'])) {
              throw new RuleException('RuleRequiredError', 'bank_account');
          }

          //p($validated);die;

          $result = AdminUserBankFacade::addUserBank(f($validated), $admin);
        }

        return $result;
    }


    /**
     * 设置默认银行卡
     *
     * @param Request $request
     * @return void
     */
    public function setUserDefaultBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'id' => ['bail', new Required, new Numeric],
                    'user_id' => ['bail', new Required, new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           if (!isset($validated['user_id'])) {
               throw new RuleException('RuleRequiredError', 'user_id');
           }

            $result = AdminUserBankFacade::setUserDefaultBank(f($validated), $admin);
        }

        return $result;
    }


    /**
     * 删除用户银行卡
     *
     * @param Request $request
     * @return void
     */
    public function deleteUserBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                     'id' => ['bail', new Required, new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['id'])) {
               throw new RuleException('RuleRequiredError', 'id');
           }

           $result = AdminUserBankFacade::deleteUserBank(f($validated), $admin);
        }

        return $result;
    }

    /**
    * 获取用户 银行卡
    *
    * @param Request $request
    * @return void
    */
    public function getUserBank(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($admin)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'user_id' => ['bail', new Required, new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['user_id'])) {
               throw new RuleException('RuleRequiredError', 'user_id');
           }

            $result = AdminUserBankFacade::getUserBank(f($validated), $admin);
        }

        return $result;
    }
}
