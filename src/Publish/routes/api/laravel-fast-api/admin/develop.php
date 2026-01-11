<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:03:05
 * @FilePath: \routes\api\laravel-fast-api\admin\develop.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Develop';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin/develop')->group(function()
    {
		
		 /**
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Develop\DevelopController
           */
            Route::controller(DevelopController::class)->group(function()
            {
                //获取
                Route::post('addDeveloper','addDeveloper');
            });

   });
});

Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});
