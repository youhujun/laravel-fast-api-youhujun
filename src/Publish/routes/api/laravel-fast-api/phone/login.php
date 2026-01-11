<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 13:35:46
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-03-01 12:42:02
 * @FilePath: \routes\api\laravel-fast-api\phone\login.php
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

        Route::namespace('Login')->group(function()
        {
			 /**
             * 注册
             * bind
             * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\RegisterController
            */
            Route::controller(RegisterController::class)->group(function()
            {
                //发送用户注册码
                Route::any('sendPhoneRegisterCode','sendPhoneRegisterCode')->withoutMiddleware('phone.login');

                //用户注册
                Route::any('userRegister','userRegister')->withoutMiddleware('phone.login');

            });
            /**
             * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\LoginController
             */
            Route::controller(LoginController::class)->group(function()
            {
                //手机号密码登录
                Route::any('loginByPhonePassword','loginByPhonePassword')->withoutMiddleware('phone.login');
                //发送验证码
                Route::any('sendVerifyCode','sendVerifyCode')->withoutMiddleware('phone.login');
                //通过手机验证码登录
                Route::any('loginByPhoneCode','loginByPhoneCode')->withoutMiddleware('phone.login');

				//发送验证码 忘记密码
                Route::any('sendPasswordCode','sendPasswordCode')->withoutMiddleware('phone.login');

				//重置手机密码
				Route::any('restPasswordByPhone','restPasswordByPhone')->withoutMiddleware('phone.login');

				//通过用户id登录,开发测试用
                Route::any('loginByUserId','loginByUserId')->withoutMiddleware('phone.login');
                
                // app 一键登录 暂时不用
                // Route::any('univerifyLogin','univerifyLogin')->withoutMiddleware('phone.login');

                //登录获取用户信息
                Route::any('getUserInfo','getUserInfo');

                //检测是否登录
                Route::any('checkIsLogin','checkIsLogin');

				//发送绑定验证码
                Route::any('sendBindCode','sendBindCode');
				//绑定手机号
				Route::any('bindPhone','bindPhone');

                //退出
                Route::any('logout','logout');


            });

			//抖音开发业务
			Route::namespace('DouYin')->group(function()
			{
				Route::prefix('douyin')->group(function()
				{
					/**
					 * 小游戏
					 */
					Route::prefix('minigame')->group(function()
					{

						/**
						 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin\LoginController
						 */
						Route::controller(LoginController::class)->group(function()
						{
							//小游戏登录
							Route::any('getOpenIdByCodeWithMiniGame','getOpenIdByCodeWithMiniGame')->withoutMiddleware('phone.login');
						});
						
					});
					
					/**
					 * 小程序
					 */
					Route::prefix('miniprogram')->group(function()
					{

						/**
						 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\DouYin\LoginController
						 */
						Route::controller(LoginController::class)->group(function()
						{
							//小程序登录
							Route::any('getOpenIdByCodeWithMiniProgram','getOpenIdByCodeWithMiniProgram')->withoutMiddleware('phone.login');
						});
						
					});
				});
			});

			/**
			 * 微信业务开发
			 */
			Route::namespace('Wechat')->group(function()
			{
				Route::prefix('wechat')->group(function()
				{

					/**
					 * 微信公众号
					 */
					Route::prefix('official')->group(function()
					{
						/**
						 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat\LoginController
						 */
						Route::controller(LoginController::class)->group(function()
						{

							//微信公众号未登录-1获取授权链接
                			Route::any('toGetCodeUrl','toGetCodeUrl')->withoutMiddleware('phone.login');

							//微信公众号未登录-2通过code授权登录
                			Route::any('authToLoginByCode','authToLoginByCode')->withoutMiddleware('phone.login');

							//微信公众号已登录-1获取授权链接
                			Route::any('toGetCodeUrlWithLogin','toGetCodeUrlWithLogin');
							//微信公众号已登录-2通过code授权登录
                			Route::any('authToLoginByCodeWithLogin','authToLoginByCodeWithLogin');

						});
					});

					/**
					 * 小程序
					 */
					Route::prefix('miniprogram')->group(function()
					{
						/**
						 * @see \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat\LoginController
						 */
						Route::controller(LoginController::class)->group(function()
						{
							//微信小程序登录
							Route::any('getOpenIdByCodeWithMiniProgram','getOpenIdByCodeWithMiniProgram')->withoutMiddleware('phone.login');
						});
					});

					/**
					 * 小游戏
					 */
					Route::prefix('minigame')->group(function()
					{
						/**
						 * @see  \App\Http\Controllers\LaravelFastApi\V1\Phone\Login\Wechat\LoginController
						 */
						Route::controller(LoginController::class)->group(function()
						{
							//微信小游戏登录
							Route::any('getOpenIdByCodeWithMiniGame','getOpenIdByCodeWithMiniGame')->withoutMiddleware('phone.login');
						});
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


