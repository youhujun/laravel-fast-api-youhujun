<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-08 14:09:11
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 07:32:55
 * @FilePath: \youhu-laravel-api-12\app\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\PersonalController.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin;

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
use App\Rules\Common\Phone;
use App\Exceptions\Common\RuleException;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Personal\UpdatePhoneDTO;
use App\DTOs\LaravelFastApi\V1\Admin\User\Admin\Personal\UpdatePasswordDTO;
use App\Facades\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\User\Admin\AdminPersonalFacadeService
 */
class PersonalController extends Controller
{
    /**
     * 确认修改头像
     */
    public function updateAvatar(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            // p('测试');
            // die;

            $result =  AdminPersonalFacade::updateAvatar($adminObject);
        }

        return $result;
    }

    /**
     * 确认修改头像
     */
    public function updatePhone(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePhoneDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            //p($validated);die;

            $result =  AdminPersonalFacade::updatePhone($requestDTO, $adminObject);
        }

        return $result;
    }

    /**
     * 确认修改头像
     */
    public function updatePassword(Request $request)
    {
        $result = code(\config('admin_code.AdminAuthError'));

        $adminObject = Auth::guard('admin_token')->user();

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $requestDTO = (new UpdatePasswordDTO())->validate($request->all());

            // p($requestDTO);
            // die;

            // p($validated);die;

            $result =  AdminPersonalFacade::updatePassword($requestDTO, $adminObject);
        }

        return $result;
    }
}
