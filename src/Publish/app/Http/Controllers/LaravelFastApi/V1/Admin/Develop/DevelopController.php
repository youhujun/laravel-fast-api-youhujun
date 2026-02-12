<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-21 03:06:13
 * @FilePath: \app\Http\Controllers\LaravelFastApi\V1\Admin\Develop\DevelopController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace App\Http\Controllers\LaravelFastApi\V1\Admin\Develop;

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

use App\Rules\LaravelFastApi\V1\Admin\Login\Password;

use App\Facades\LaravelFastApi\V1\Admin\Develop\DeveloperFacade;

class DevelopController extends Controller
{

    /**
     * 添加
     *
     * @param Request $request
     * @return void
     */
    public function addDeveloper(Request $request)
    {
        $admin = Auth::guard('admin_token')->user();

        $result = code(\config('code.AdminAuthError'));

        if(Gate::forUser($admin)->allows('admin-role'))
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

            $result =  DeveloperFacade::addDeveloper($admin,f($validated));
        }

        return $result;
    }


}
