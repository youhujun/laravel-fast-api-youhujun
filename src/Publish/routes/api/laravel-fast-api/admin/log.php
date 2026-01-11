<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-16 13:51:34
 * @FilePath: \routes\api\laravel-fast-api\admin\log.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Log';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {
		/**
            *后台日志
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\AdminLogController
            */
        Route::prefix('log')->controller(AdminLogController::class)->group(function()
        {

            //获取管理员登录日志
            Route::post('getAdminLoginLog','getAdminLoginLog');
            //删除管理员登录日志
            Route::post('deleteAdminLoginLog','deleteAdminLoginLog');
            //批量删除管理员登录日志
            Route::post('multipleDeleteAdminLoginLog','multipleDeleteAdminLoginLog');

            //获取管理员事件日志
            Route::post('getAdminEventLog','getAdminEventLog');
            //删除管理员事件日志
            Route::post('deleteAdminEventLog','deleteAdminEventLog');
            //批量删除管理员事件日志
            Route::post('multipleDeleteAdminEventLog','multipleDeleteAdminEventLog');

        });

        /**
         * 用户日志
         * bind
         * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Log\UserLogController
         */
        Route::prefix('log')->controller(UserLogController::class)->group(function()
        {
            //获取用户登录日志
            Route::post('getUserLoginLog','getUserLoginLog');
            //删除用户登录日志
            Route::post('deleteUserLoginLog','deleteUserLoginLog');
            //批量删除用户登录日志
            Route::post('multipleDeleteUserLoginLog','multipleDeleteUserLoginLog');

            //获取用户事件日志
            Route::post('getUserEventLog','getUserEventLog');
            //删除用户事件日志
            Route::post('deleteUserEventLog','deleteUserEventLog');
            //批量删除用户事件日志
            Route::post('multipleDeleteUserEventLog','multipleDeleteUserEventLog');
        });

   });
});


Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});

