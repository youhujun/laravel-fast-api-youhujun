<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-01-17 15:13:54
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-12-07 11:33:12
 * @FilePath: \database\seeders\LaravelFastApi\LaravelFastApiSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

//---------------系统设置开始-----------------
//|--系统设置
//|--|--系统配置
//系统参数空模版
use Database\Seeders\LaravelFastApi\System\SystemConfig\SystemConfigSeeder;
//后台提示音配置
use Database\Seeders\LaravelFastApi\System\SystemConfig\VoiceConfigSeeder;

//|--|--菜单路由z
use Database\Seeders\LaravelFastApi\System\PermissionSeeder;
//|--|--角色管理
use Database\Seeders\LaravelFastApi\System\RoleSeeder;
//|--|--|++建立角色和菜单权限
use Database\Seeders\LaravelFastApi\System\RolePermissionSeeder;
//|--|--分类管理
//|--|--|--文章分类
use Database\Seeders\LaravelFastApi\Service\Group\Category\CategorySeeder;
//|--|--级别管理
//|--|--|--|级别配置项
use Database\Seeders\LaravelFastApi\Service\Level\LevelItemSeeder;
//|--|--|--|级别管理
use Database\Seeders\LaravelFastApi\Service\Level\LevelSeeder;
//|--|--|--|++建立级别和级别的关系
use Database\Seeders\LaravelFastApi\Service\Level\LevelItemUnionSeeder;
//|--|--地区管理
use Database\Seeders\LaravelFastApi\System\RegionSeeder;
//|--|--银行管理
use Database\Seeders\LaravelFastApi\System\BankSeeder;
//---------------系统设置结束-----------------
//|--系统设置
//|--|--通用设置
//|--|--|--集合设置
use Database\Seeders\LaravelFastApi\System\SystemConfig\WithdrawConfigSeeder;

//|--图片空间
//|--|--我的相册
use Database\Seeders\LaravelFastApi\Picture\AlbumSeeder;
//|--|--|++相册图片
use Database\Seeders\LaravelFastApi\Picture\AlbumPictureSeeder;

//------------------用户管理开始-----------------
//|--用户管理
//|--|--用户管理
use Database\Seeders\LaravelFastApi\User\UserSeeder;
//|--|--|++用户角色处理
use Database\Seeders\LaravelFastApi\User\UserRoleUnionSeeder;
//|--|--|++用户账户
use Database\Seeders\LaravelFastApi\User\UserAmountSeeder;
//|--|--|++用户详情
use Database\Seeders\LaravelFastApi\User\UserInfoSeeder;
//|--|--管理员管理
use Database\Seeders\LaravelFastApi\User\AdminSeeder;

//------------------用户管理结束-----------------

class LaravelFastApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		//如果开启这个填充,才会执行
		if(config('youhujun.fill_system_config'))
		{
			$this->call([
				//|--|--系统配置-参数配置
				//如果未开启shop,才执行这个填充
				SystemConfigSeeder::class,
			]);
		}
         //|--系统设置
       $this->call([

			////|--|--系统配置-提示配置
			VoiceConfigSeeder::class,
            //|--|--菜单管理
            PermissionSeeder::class,
            //|--|--角色管理
            RoleSeeder::class,
            //|--|--|++绑定角色和菜单的关系
            RolePermissionSeeder::class,
            //|--|--|--|--文章分类
            CategorySeeder::class,
            //|--|--级别管理
            //|--|--|--|级别配置项
            LevelItemSeeder::class,
            //|--|--|--|级别管理
            LevelSeeder::class,
            //|--|--|--|++建立级别和级别的关系
            LevelItemUnionSeeder::class,
            //|--|--地区管理
            RegionSeeder::class,
            //|--|--银行管理
            BankSeeder::class,
       ]);

       //|--图片空间
       $this->call([
            //|--|--我的相册
            AlbumSeeder::class,
            //|--|--|++相册图片
            AlbumPictureSeeder::class,
       ]);

	   //|--业务设置
	   $this->call([
			//|--|--通用设置
			//|--|--|--集合设置
			WithdrawConfigSeeder::class
	   ]);

       //|--用户管理
       $this->call(
        [
           //|--|--用户管理
           UserSeeder::class,
           //|--|--|++绑定用户管理和角色的关系
           UserRoleUnionSeeder::class,
		   //|--|--|++用户账户
		   UserAmountSeeder::class,
           //|--|--|++用户详情
           UserInfoSeeder::class,
           //|--|--管理员管理
           AdminSeeder::class,

        ]);
    }
}
