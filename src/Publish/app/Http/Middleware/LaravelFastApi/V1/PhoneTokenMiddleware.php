<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 15:49:23
 * @FilePath: \youhu-laravel-api-12\app\Http\Middleware\LaravelFastApi\V1\PhoneTokenMiddleware.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace App\Http\Middleware\LaravelFastApi\V1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneTokenMiddleware
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        $userObject = Auth::guard('phone_token')->user();

        if (empty($userObject)) {
            return response()->json(code(\config('phone_code.PhoneTokenError')));
        }

        $request->attributes->add(['user' => $userObject]);

        return $next($request);
    }
}
