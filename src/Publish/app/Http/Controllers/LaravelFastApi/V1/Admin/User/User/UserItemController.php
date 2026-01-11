<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: lak 15931400746@163.com
 * @Date: 2023-08-14 11:12:53
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-06-21 17:20:56
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserItemController.php
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
use App\Exceptions\Common\RuleException;

use App\Facade\LaravelFastApi\V1\Admin\User\User\AdminUserItemFacade;

class UserItemController extends Controller
{
     /**
     * 获取默认用户选项
     *
     * @param Request $request
     * @return void
     */
    public function getDefaultUser(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $user = Auth::guard('admin_token')->user();

        if (Gate::forUser($user)->allows('admin-role')) {
            $validator = Validator::make(
                $request->all(),
                [
                    // 是否认证
                    'real_auth_status' => ['bail', 'nullable', new Numeric],
                ],
                []
            );

            $validated = $validator->validated();

           if (!isset($validated['find'])) {
               throw new RuleException('RuleRequiredError', 'find');
           }

            //p($validated);die;

            $result =  AdminUserItemFacade::getDefaultUser(f($validated), $user);
        }

        return $result;
    }

    /**
     * 查找选项
     *
     * @param Request $request
     * @return void
     */
    public function findUser(Request $request)
    {
        $user = Auth::guard('admin_token')->user();

        $result = code(\config('admin_code.AdminAuthError'));

        if (Gate::forUser($user)->allows('admin-role')) {

            $validator = Validator::make(
                $request->all(),
               [
                    'find' => ['bail', new Required],
                    // 是否认证
                    'real_auth_status' => ['bail', 'nullable', new Numeric],
               ],
               []
           );

           $validated = $validator->validated();

           if (!isset($validated['find'])) {
               throw new RuleException('RuleRequiredError', 'find');
           }

            //p($validated);die;

            $result = AdminUserItemFacade::findUser(f($validated), $user);
        }

        return $result;
    }
}
