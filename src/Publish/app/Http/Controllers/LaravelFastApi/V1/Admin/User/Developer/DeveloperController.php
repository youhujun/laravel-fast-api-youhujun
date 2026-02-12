<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-17 16:32:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:54:15
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\User\Developer\DeveloperController.php
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

//自定义验证规则
use App\Rules\Common\Required;
use App\Rules\Common\Numeric;
use App\Rules\Common\CheckString;
use App\Rules\Common\CheckBetween;
use App\Rules\Common\CheckUnique;
use App\Exceptions\Common\RuleException;

//后台用户密码
use App\Rules\LaravelFastApi\V1\Admin\User\User\Password;

//开发者门面
use App\Facades\LaravelFastApi\V1\Admin\User\Developer\Developer;

class DeveloperController extends Controller
{
     /**
     * 添加开发者
     */
    public function addDeveloper(Request $request)
    {
        $result = code(\config('code.apiAuthError'));

        $validator = Validator::make(
            $request->all(),
            [
                'username'=>['bail','nullable',new Required,new CheckString,new CheckBetween(1,20),new CheckUnique('users','account_name')],
                'password'=>['bail','nullable',new Required,new CheckString,new CheckBetween(6,10),new Password]
            ],
            []
        );

        $validated = $validator->validated();

        if (!isset($validated['username'])) {
            throw new RuleException('RuleRequiredError', 'username');
        }

        if (!isset($validated['password'])) {
            throw new RuleException('RuleRequiredError', 'password');
        }

        //p($validated);die;

        $result =  Developer::addDeveloper(f($validated));


        return $result;
    }



}
