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
use App\DTOs\LaravelFastApi\V1\Admin\Develop\AddDevelpDTO;
use App\Facades\LaravelFastApi\V1\Admin\Develop\DeveloperFacade;

/**
 * @see \App\Services\Facade\LaravelFastApi\V1\Admin\Develop\DeveloperFacadeService
 */
class DevelopController extends Controller
{
    public function addDeveloper(Request $request)
    {
        $adminObject = Auth::guard('admin_token')->user();

        $result = code(\config('code.AdminAuthError'));

        if (Gate::forUser($adminObject)->allows('admin-role')) {
            $addDevelopDTO = (new AddDevelpDTO())->validate($request->all());
            // p($addDevelopDTO);
            // die;

            $result =  DeveloperFacade::addDeveloper($adminObject, $addDevelopDTO);
        }

        return $result;
    }
}
