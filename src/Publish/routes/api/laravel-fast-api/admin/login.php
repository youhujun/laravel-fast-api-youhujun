<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-09 20:54:01
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-06-21 00:54:50
 * @FilePath: \routes\api\laravel-fast-api\admin\login.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Login';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {
		Route::prefix('login')->group(function()
		{
			/**
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginController
           */
			Route::controller(LoginController::class)->group(function()
			{
				//获取
				Route::post('login','login')->withoutMiddleware('admin.login');
				//开发者登录--开发工作用
				Route::post('loginYouhu','login')->withoutMiddleware('admin.login');
				//退出
				Route::get('logout','logout');
				
			});
		});

		/**
		 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Login\LoginAfterController
		 */
		Route::prefix('after-login')->group(function()
		{
			Route::controller(LoginAfterController::class)->group(function()
			{
				//获取登录用户信息
				Route::get('getAdminInfo','getAdminInfo');

				//获取系统属性路由
				Route::get('getTreePermission','getTreePermission');

				//获取所有提示配置
				Route::any('getAllSystemVoiceConfig','getAllSystemVoiceConfig');
			});
		});
		   

   });
});

Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});
