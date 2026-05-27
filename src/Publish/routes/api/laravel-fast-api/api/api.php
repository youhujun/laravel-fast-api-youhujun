<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-03-30 22:35:47
 * @FilePath: \youhu-laravel-api-12\routes\api\laravel-fast-api\api\api.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Api\\';

/**
 * 后端模版
 */
Route::prefix(config('custom.version'))->namespace($namespace)->group(function () {
    Route::prefix('base')->group(function () {
        //获取临时凭证
        /**
        * @see \App\Http\Controllers\LaravelFastApi\V1\Api\Auth\AuthController
        */
        Route::prefix('auth')->namespace('Auth')->middleware('youhubase.sign')->controller(AuthController::class)->group(function () {
            //获取临时凭证
            Route::any('getAccessToken', 'getAccessToken');
        });

        /**
         * 测试
         * bind
         * @see App\Http\Controllers\LaravelFastApi\V1\Api\Test\TestController
         */
        Route::middleware('youhubase.auth')->group(function () {
            Route::prefix('test')->namespace('Test')->group(function () {
                Route::controller(TestController::class)->group(function () {
                    //获取文章
                    Route::any('test', 'test');
                });
            });
        });
    });
});


Route::fallback(function () {
    $data = ['code' => 500,'msg' => '路由错误'];

    return $data;
});
