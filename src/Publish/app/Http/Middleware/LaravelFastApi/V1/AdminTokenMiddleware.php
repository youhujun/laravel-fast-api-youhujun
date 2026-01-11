<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-12 08:42:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 12:07:54
 * @FilePath: \app\Http\Middleware\LaravelFastApi\V1\AdminTokenMiddleware.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */



namespace App\Http\Middleware\LaravelFastApi\V1;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $admin = Auth::guard('admin_token')->user();

        if(empty($admin))
        {
            return response()->json(code(\config('admin_code.AdminTokenError')));
        }

        $request->attributes->add(['admin'=>$admin]);

        return $next($request);
    }
}
