<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-16 11:57:12
 * @FilePath: \routes\api\laravel-fast-api\admin\article.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\Article';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {
		   /**
              * 文章
              * bind
              * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\Article\ArticleController
            */
            Route::prefix('article')->controller(ArticleController::class)->group(function()
            {

                //获取文章
                Route::post('getArticle','getArticle');

                //添加文章
                Route::post('addArticle','addArticle');

                //修改文章
                Route::post('updateArticle','updateArticle');

                //批量置顶
                Route::post('multipleToTopArticle','multipleToTopArticle');

                //批量取消置顶
                Route::post('multipleUnTopArticle','multipleUnTopArticle');

                //置顶文章
                Route::post('toTopArticle','toTopArticle');

                //删除文章
                Route::post('deleteArticle','deleteArticle');

                //批量删除
                Route::post('multipleDeleteArticle','multipleDeleteArticle');

            });
   });
});




Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});


