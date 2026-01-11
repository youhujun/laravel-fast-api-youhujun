<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-10 18:51:17
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 16:03:37
 * @FilePath: \routes\api\laravel-fast-api\admin\system.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


/**模板路由 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$namespace = 'App\\Http\\Controllers\\LaravelFastApi\\V1\\Admin\\System';

/**
 * 后端模版
 */
 Route::prefix(config('custom.version'))->namespace($namespace)->middleware('admin.login')->group(function()
{
    Route::prefix('admin')->group(function()
    {
		// 后台管理-系统设置-菜单管理
		Route::prefix('permission')->group(function()
		{
			Route::namespace('Permission')->group(function()
			{
				/**
				* bind
				* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Permission\PermissionController
				*/
				Route::controller(PermissionController::class)->group(function()
				{
					//添加路由
					Route::any('getTreeMenu','getTreeMenu');
					//获取树形菜单选项
					Route::any('getChildrenOptions','getChildrenOptions');
					//获取单个菜单表单数据
					Route::any('getSingleMenuForm','getSingleMenuForm');
					//添加路由
					Route::any('addMenu','addMenu');
					//更新路由
					Route::any('updateMenu','updateMenu');
					//移动菜单
					Route::any('moveMenu','moveMenu');
					//更新路由
					Route::any('deleteMenu','deleteMenu');
					//修改路由状态
					Route::any('switchMenu','switchMenu');


				});
			});
		});

		//后台管理-系统设置-平台配置
		Route::prefix('platform')->group(function()
		{
			Route::namespace('Platform')->group(function()
			{

				//后台管理-系统设置-平台配置-缓存设置
				Route::prefix('cache')->group(function()
				{
					/**
					* bind
					* @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Platform\CacheConfigController
					*/
					Route::controller(CacheConfigController::class)->group(function()
					{
						//系统缓存
						//清理全部配置缓存
						Route::any('cleanConfigCache','cleanConfigCache');
						//清理地区缓存
						Route::any('cleanRegionCache','cleanRegionCache');
						//清理角色缓存
						Route::any('cleanRoleCache','cleanRoleCache');
						//清理产品分类缓存
						Route::any('cleanGoodsClassCache','cleanGoodsClassCache');
						//清理文章分类缓存
						Route::any('cleanCategoryCache','cleanCategoryCache');
						//清理标签分类缓存
						Route::any('cleanLabelCache','cleanLabelCache');
						//清理系统配置缓存
						Route::any('cleanSystemConfigCache','cleanSystemConfigCache');
						//清理权限路由缓存
						Route::any('cleanPermissionCache','cleanPermissionCache');
						//用户缓存
						//清理用户信息缓存
						Route::any('cleanLoginUserInfoCache','cleanLoginUserInfoCache');

					});
				});
			});

		});

        
        //后台管理-系统设置-系统配置
        Route::prefix('system')->namespace('SystemConfig')->group(function()
        {
            /**
             * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\SystemConfigController
             */
            Route::prefix('config')->controller(SystemConfigController::class)->group(function()
            {
                //获取系统配置
                Route::post('getSystemConfig','getSystemConfig');
                //添加系统配置
                Route::post('addSystemConfig','addSystemConfig');
                //更新系统配置
                Route::post('updateSystemConfig','updateSystemConfig');
                //删除系统配置
                Route::post('deleteSystemConfig','deleteSystemConfig');
                // 批量删除
                Route::post('multipleDeleteSystemConfig','multipleDeleteSystemConfig');
            });


            /**
             * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\VoiceConfigController
             */
            Route::prefix('voice')->controller(VoiceConfigController::class)->group(function()
            {
                //获取提示配置
                Route::post('getVoiceConfig','getVoiceConfig');
                //添加提示配置
                Route::post('addVoiceConfig','addVoiceConfig');
                //更新提示配置
                Route::post('updateVoiceConfig','updateVoiceConfig');
                //删除提示配置
                Route::post('deleteVoiceConfig','deleteVoiceConfig');
                // 批量删除
                Route::post('multipleDeleteVoiceConfig','multipleDeleteVoiceConfig');
            });

			/**
             * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\WithdrawConfigController
             */
            Route::prefix('withdraw')->controller(WithdrawConfigController::class)->group(function()
            {
                //获取系统提现配置
                Route::any('getWithdrawConfig','getWithdrawConfig');
                //更新系统提现配置
                Route::any('updateWithdrawConfig','updateWithdrawConfig');
            });

			//后台管理-系统设置-系统配置-三方平台
			Route::namespace('OtherPlatform')->group(function()
			{
				Route::prefix('other-platform')->group(function()
				{
					/**
					 *  系统微信配置
					 *@see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatForm\SystemWechatConfigController
						*/
					Route::controller(SystemWechatConfigController::class)->group(function()
					{
						//获取系统微信配置列表
						Route::any('getSystemWechatConfig','getSystemWechatConfig');
						//添加系统微信配置
						Route::post('addSystemWechatConfig','addSystemWechatConfig');
						//修改系统微信配置
						Route::post('updateSystemWechatConfig','updateSystemWechatConfig');
						//删除系统微信配置
						Route::post('deleteSystemWechatConfig','deleteSystemWechatConfig');
						//批量删除系统微信配置
						Route::post('multipleDeleteSystemWechatConfig','multipleDeleteSystemWechatConfig');
					});

					/**
					 *  系统抖音配置
					 *@see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\SystemConfig\OtherPlatform\SystemDouyinConfigController
						*/
					Route::controller(SystemDouyinConfigController::class)->group(function()
					{
						//获取系统抖音配置列表
						Route::any('getSystemDouyinConfig','getSystemDouyinConfig');
						//添加系统抖音配置
						Route::post('addSystemDouyinConfig','addSystemDouyinConfig');
						//修改系统抖音配置
						Route::post('updateSystemDouyinConfig','updateSystemDouyinConfig');
						//删除系统抖音配置
						Route::post('deleteSystemDouyinConfig','deleteSystemDouyinConfig');
						//批量删除系统抖音配置
						Route::post('multipleDeleteSystemDouyinConfig','multipleDeleteSystemDouyinConfig');
					});
				});
			});
			
        });

        // 后台管理-系统设置-角色管理
        Route::prefix('role')->namespace('Role')->group(function()
        {
            /**
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Role\RoleController
            */
            Route::controller(RoleController::class)->group(function()
            {
                //添加路由
                Route::get('getTreeRole','getTreeRole');
                //更新路由
                Route::post('addRole','addRole');
                //移动菜单
                Route::post('updateRole','updateRole');
                //更新路由
                Route::post('moveRole','moveRole');
                //修改路由状态
                Route::post('deleteRole','deleteRole');
                //修改角色权限
                Route::post('resetRolePermission','resetRolePermission');

            });
        });

		// 后台管理-系统设置-地区管理
        Route::prefix('region')->namespace('Region')->group(function()
        {
            /**
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Region\RegionController
            */
            Route::controller(RegionController::class)->group(function()
            {
                 //获取树形地区
                Route::get('getTreeRegions','getTreeRegion');
                //获取所有地区
                Route::get('getRegions','getAllRegion');
                //添加地区
                Route::post('addRegion','addRegion');
                //更新地区
                Route::post('updateRegion','updateRegion');
                //移动地区
                Route::post('moveRegion','moveRegion');
                //删除地区
                Route::post('deleteRegion','deleteRegion');

            });
        });

		// 后台管理-系统设置-银行管理
        Route::prefix('bank')->namespace('Bank')->group(function()
        {
            /**
            * bind
            * @see \App\Http\Controllers\LaravelFastApi\V1\Admin\System\Bank\BankController
            */
            Route::controller(BankController::class)->group(function()
            {
                //获取常用银行
                Route::get('defaultBank','defaultBank');
                //查找银行
                Route::post('findBank','findBank');
                //获取银行
                Route::post('getBank','getBank');
                //添加银行
                Route::post('addBank','addBank');
                //更新银行
                Route::post('updateBank','updateBank');
                //删除银行
                Route::post('deleteBank','deleteBank');
                //批量删除银行
                Route::post('multipleDeleteBank','multipleDeleteBank');

            });
        });
        
   });
});


Route::fallback(function ()
{
    $data = ['code'=>500,'msg'=>'路由错误'];

    return $data;
});


