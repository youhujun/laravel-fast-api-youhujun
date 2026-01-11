<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 13:35:46
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-09-03 19:23:58
 * @FilePath: \routes\api\phone\system.php
 */

/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Phone';

/**
 * 手机模版
 */
Route::prefix(config('custom.version'))->namespace($namespace)->middleware('phone.login')->group(function()
{
   Route::prefix('phone')->group(function()
    {
        Route::namespace('System')->group(function()
        {
            /**
             * 
             * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\System\MapController
             */
            Route::controller(MapController::class)->group(function()
            {
                //通过经纬度获取
                Route::post('getLocationRegionByH5','getLocationRegionByH5');
            });

			/**
			 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\System\RegionController
			 */
			Route::controller(RegionController::class)->group(function()
			{
				//通过经纬度获取
				Route::any('getRegionById','getRegionById');

				//获取树形地区
				Route::any('getTreeRegions','getTreeRegions');
			});

			/**
			 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\System\GoodsClassController
			 */
			Route::controller(GoodsClassController::class)->group(function()
			{
				// 获取树形分类
				Route::any('getTreeGoodsClass','getTreeGoodsClass');

				// 获取下级分类通过id
				Route::any('getGoodsClassById','getGoodsClassById');
				
			});
			
        });
   });
});


Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});
