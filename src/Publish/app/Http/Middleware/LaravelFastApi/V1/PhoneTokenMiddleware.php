<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 12:08:46
 * @FilePath: \app\Http\Middleware\LaravelFastApi\V1\PhoneTokenMiddleware.php
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
        $user = Auth::guard('phone_token')->user();

        if(empty($user))
        {
            return response()->json(code(\config('phone_code.PhoneTokenError')));
        }

        $request->attributes->add(['user'=>$user]);

        return $next($request);
    }
}
