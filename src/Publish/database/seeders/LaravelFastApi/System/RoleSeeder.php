<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-01-06 12:36:10
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-14 18:17:46
 * @FilePath: \database\seeders\LaravelFastApi\System\RoleSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$this->runOneDeepData();
		$this->runTwoDeepData();
    }

	/**
	 * 填充一级数据
	 */
	public function runOneDeepData()
	{
		//一级角色
        $oneDeepData = [
            //10
            ['id'=>10,'role_name'=>'开发者','logic_name'=>'develop','parent_id'=>0,'deep'=>1,'switch'=>1,'created_time'=>time()],
            //20
            ['id'=>20,'role_name'=>'超级管理员','logic_name'=>'super','parent_id'=>0,'deep'=>1,'switch'=>1,'created_time'=>time()],
            //30
            ['id'=>30,'role_name'=>'管理员','logic_name'=>'admin','parent_id'=>0,'deep'=>1,'switch'=>1,'created_time'=>time()],
            //40
            ['id'=>40,'role_name'=>'用户','logic_name'=>'user','parent_id'=>0,'deep'=>1,'switch'=>1,'created_time'=>time()],
        ];

        DB::table('role')->insert($oneDeepData);
	}

	/**
	 * 填充二级数据
	 */
	public function runTwoDeepData()
	{
		//二级角色
        $twoDeepData = [
			//30管理员
            ['id'=>100,'role_name'=>'配置管理员','logic_name'=>'config_admin','parent_id'=>30,'deep'=>2,'switch'=>1,'created_time'=>time()],
            ['id'=>200,'role_name'=>'相册管理员','logic_name'=>'album_admin','parent_id'=>30,'deep'=>2,'switch'=>1,'created_time'=>time()],
            ['id'=>300,'role_name'=>'订单管理员','logic_name'=>'order_admin','parent_id'=>30,'deep'=>2,'switch'=>1,'created_time'=>time()],
            ['id'=>400,'role_name'=>'文章管理员','logic_name'=>'article_admin','parent_id'=>30,'deep'=>2,'switch'=>1,'created_time'=>time()],
            
        ];

        DB::table('role')->insert($twoDeepData);
	}
}
