<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 13:35:46
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-24 17:22:29
 * @FilePath: \routes\api\laravel-fast-api\phone\user.php
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

        Route::namespace('User\\User')->group(function()
        {
            /**
             * 手机端用户控制器
             * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\UserController
             */
            Route::controller(UserController::class)->group(function()
            {
                //申请实名认证
                Route::post('realAuthApply','realAuthApply');
            });

			//我的
            Route::namespace('My')->group(function()
            {
                /**
                 *@see \App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\ConfigController
                 */
                Route::controller(ConfigController::class)->group(function()
                {
                    //清理用户缓存
                    Route::any('clearUserCache','clearUserCache');
                });

				/**
                 *@see \App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\UserController
                 */
                Route::controller(UserController::class)->group(function()
                {
                  
					//修改用户头像
                    Route::any('uploadUserAvatar','uploadUserAvatar');
					//修改用户昵称
                    Route::any('updateUserNickName','updateUserNickName');
					//修改手机号
                    Route::any('updateUserPhone','updateUserPhone');

                });

				//我的-我的地址
				Route::namespace('Address')->group(function()
				{
					/**
					 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\User\User\My\Address\UserAddressController
					 */
					Route::controller(UserAddressController::class)->group(function()
					{
						//获取地址
						Route::any('getUserAddress','getUserAddress');

						//添加地址
						Route::any('addUserAddress','addUserAddress');

						//更新地址
						Route::any('updateUserAddress','updateUserAddress');

						//删除地址
						Route::any('deleteUserAddress','deleteUserAddress');

						//设置默认地址
						Route::any('setDefaultUserAddress','setDefaultUserAddress');

						//置顶地址
						Route::any('setTopUserAddress','setTopUserAddress');

						//订单用-获取用户默认地址
						Route::any('getUserDefaultAddress','getUserDefaultAddress');
					});
				});
            });

        });
   });
});


Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});
