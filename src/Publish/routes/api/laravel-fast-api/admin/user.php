<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-08 14:23:19
 * @FilePath: \routes\api\laravel-fast-api\admin\user.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\User';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {

			// 管理员
            Route::prefix('admin')->namespace('Admin')->group(function()
            {
				Route::prefix('personal')->group(function()
				{
					/**
					 * 个人中心
					 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\PersonalController
					 */
					Route::controller(PersonalController::class)->group(function()
					{
						//修改头像
						Route::any('updateAvatar','updateAvatar');
						//修改手机号
						Route::any('updatePhone','updatePhone');
						//修改手机号
						Route::any('updatePassword','updatePassword');
						
					});
				});

				Route::prefix('administrator')->group(function()
				{
					/**
					 * 管理员管理
					 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\Admin\AdministratorController
					 */
					Route::controller(AdministratorController::class)->group(function()
					{
						//获取所有管理员用户信息
						Route::any('getDefaultAdmin','getDefaultAdmin');
						//查找管理员信息
						Route::any('findAdmin','findAdmin');
						//获取管理员
						Route::any('getAdmin','getAdmin');
						//添加管理员
						Route::any('addAdmin','addAdmin');
						//更新管理员
						Route::any('updateAdmin','updateAdmin');
						//删除管理员
						Route::any('deleteAdmin','deleteAdmin');
						//批量删除管理员
						Route::any('multipleDeleteAdmin','multipleDeleteAdmin');
						//禁用管理员
						Route::any('disableAdmin','disableAdmin');
						//批量禁用管理员
						Route::any('multipleDisableAdmin','multipleDisableAdmin');

					});
				});

				


                
            });

            Route::prefix('user')->namespace('User')->group(function()
            {
                /**
                 * 用户管理
                 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserController
                 */
                Route::controller(UserController::class)->group(function()
                {
                    //获取用户
                    Route::any('getUser','getUser');
                    //添加用户 注册用户
                    Route::any('addUser','addUser');
                    //删除用户
                    Route::any('deleteUser','deleteUser');
                    //批量删除用户
                    Route::any('multipleDeleteUser','multipleDeleteUser');
                    //禁用用户
                    Route::any('disableUser','disableUser');
                    //批量禁用用户
                    Route::any('multipleDisableUser','multipleDisableUser');

                });

                /**
                * 用户选项
                * bind
                * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserItemController
                    */
                Route::prefix('user-item')->controller(UserItemController::class)->group(function()
                {
                    //获取选项用户
                    Route::any('getDefaultUser','getDefaultUser');
                    //查找用户
                    Route::any('findUser','findUser');
                });

                /**
				 * 用户实名认证审核
                 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserRealAuthController
                 */
                Route::prefix('real-auth')->controller(UserRealAuthController::class)->group(function()
                {
                    //获取用户实名认证申请
                    Route::any('getUserRealAuthApply','getUserRealAuthApply');

                    // 审核实名认证用户
                    Route::any('realAuthUser','realAuthUser');

                    //用户身份证
                    //设置 用户身份证
                    Route::any('setUserIdCard','setUserIdCard');
                    //获取用户身份证
                    Route::any('getUserIdCard','getUserIdCard');
                    //修改用户身份证号
                    Route::any('updateUserIdNumber','updateUserIdNumber');
                });

                /**
                * 用户详情
                * bind
                * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserDetailsController
                */
                Route::prefix('details')->controller(UserDetailsController::class)->group(function()
                {
                    //用户修改
                    //修改用户手机号
                    Route::any('updateUserPhone','updateUserPhone');
                    //修改用户真是姓名
                    Route::any('updateUserRealName','updateUserRealName');
                    //修改用户昵称
                    Route::any('updateUserNickName','updateUserNickName');
                    //修改用户性别
                    Route::any('updateUserSex','updateUserSex');
                    //更改用户级别
                    Route::any('changeUserLevel','changeUserLevel');

                    //修改用户生日
                    Route::any('updateUserBirthdayTime','updateUserBirthdayTime');
                    //重置用户密码
                    Route::any('resetUserPassword','resetUserPassword');

                    //获取用户二维码
                    Route::any('getUserQrcode','getUserQrcode');
					//生成用户二维码
                    Route::any('makeUserQrcode','makeUserQrcode');

                });

                /**
                * 用户地址
                * bind
                * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAddressController
                    */
                Route::prefix('address')->controller(UserAddressController::class)->group(function()
                {
                    //用户地址
                    //获取用户地址
                    Route::any('getUserAddress','getUserAddress');
                    //添加用户地址
                    Route::any('addUserAddress','addUserAddress');
                    //删除用户地址
                    Route::any('deleteUserAddress','deleteUserAddress');
                    //设为默认地址
                    Route::any('setDefaultUserAddress','setDefaultUserAddress');
                });

                /**
                * 用户银行卡
                * bind
                * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\user\UserBankController
                    */
                Route::prefix('bank')->controller(UserBankController::class)->group(function()
                {
                        //用户银行卡
                    //添加银行卡
                    Route::any('addUserBank','addUserBank');
                    //设置默认银行卡
                    Route::any('setUserDefaultBank','setUserDefaultBank');
                    //删除银行卡
                    Route::any('deleteUserBank','deleteUserBank');
                    //获取用户银行卡
                    Route::any('getUserBank','getUserBank');
                });

                /**
                * 用户团队
                * bind
                * @see  \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserTeamController
                    */
                Route::prefix('team')->controller(UserTeamController::class)->group(function()
                {
                    //获取用户的上级用户(推荐用户)
                    Route::any('getUserSource','getUserSource');
                    //获取用户下级团队
                    Route::any('getUserLowerTeam','getUserLowerTeam');
                });


                /**
                 * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\User\User\UserAccountController
                 */
                Route::prefix('account')->controller(UserAccountController::class)->group(function()
                {
                    //设置用户账户
                    Route::any('setUserAccount','setUserAccount');
                    //获取用户账户日志
                    Route::any('getUserAccountLog','getUserAccountLog');
					//获取用户账户信息
                    Route::any('getUserAccountInfo','getUserAccountInfo');
                });

            });


   });
});


Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});


