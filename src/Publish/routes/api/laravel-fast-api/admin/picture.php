<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-05 23:01:44
 * @FilePath: \routes\api\laravel-fast-api\admin\picture.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Picture';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {
        /**
         * 相册
        * bind
        * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\AlbumController
        */
        Route::prefix('picture')->controller(AlbumController::class)->group(function()
        {
            //获取相册
            Route::post('getAlbum','getAlbum');
            //添加相册
            Route::post('addAlbum','addAlbum');
            //更新相册
            Route::post('updateAlbum','updateAlbum');
            //删除相册
            Route::post('deleteAlbum','deleteAlbum');
            //获取相册图片
            Route::post('getAlbumPicture','getAlbumPicture');

            //获取默认选项
            Route::post('getDefaultAlbum','getDefaultAlbum');
            //查找默认相册
            Route::post('findAlbum','findAlbum');

        });

        /**
         * 相册图片
        * bind
        * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Picture\PictureController
        */
        Route::prefix('picture')->controller(PictureController::class)->group(function()
        {

            //设为封面
            Route::any('setCover','setCover');

            //转移相册
            Route::any('moveAlbum','moveAlbum');

            //批量转移相册
            Route::any('moveMultipleAlbum','moveMultipleAlbum');

            //删除图片
            Route::any('deletePicture','deletePicture');

            //批量删除图片
            Route::any('deleteMultiplePicture','deleteMultiplePicture');

            //更新图片名称
            Route::any('updatePictureName','updatePictureName');

        });
    });
});

Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});
