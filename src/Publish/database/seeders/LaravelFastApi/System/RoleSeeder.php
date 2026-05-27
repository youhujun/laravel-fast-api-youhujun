<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-01-06 12:36:10
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 04:21:28
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\System\RoleSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use App\Models\LaravelFastApi\V1\System\Role\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();

        $this->command->info('开始填充系统角色');

        $this->runOneDeepData();
        $this->runTwoDeepData();

        $this->command->info('✅填充填充系统角色完成');
    }

    /**
     * 填充一级数据
     */
    public function runOneDeepData()
    {
        Role::create([
            'id' => 10,
            'role_name' => '开发者',
            'logic_name' => 'develop',
            'parent_id' => 0,
            'deep' => 1,
            'type' => 10,
            'is_system' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 20,
            'role_name' => '超级管理员',
            'logic_name' => 'super',
            'parent_id' => 0,
            'deep' => 1,
            'is_system' => 1,
            'type' => 10,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 30,
            'role_name' => '管理员',
            'logic_name' => 'admin',
            'parent_id' => 0,
            'deep' => 1,
            'is_system' => 1,
            'type' => 10,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 40,
            'role_name' => '用户',
            'logic_name' => 'user',
            'parent_id' => 0,
            'deep' => 1,
            'is_system' => 1,
            'type' => 20,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);
    }

    /**
     * 填充二级数据
     */
    public function runTwoDeepData()
    {
        Role::create([
            'id' => 100,
            'role_name' => '配置管理员',
            'logic_name' => 'config_admin',
            'parent_id' => 30,
            'deep' => 2,
            'type' => 10,
            'is_system' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 200,
            'role_name' => '相册管理员',
            'logic_name' => 'album_admin',
            'parent_id' => 30,
            'deep' => 2,
            'type' => 10,
            'is_system' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 300,
            'role_name' => '订单管理员',
            'logic_name' => 'order_admin',
            'parent_id' => 30,
            'deep' => 2,
            'type' => 10,
            'is_system' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        Role::create([
            'id' => 400,
            'role_name' => '文章管理员',
            'logic_name' => 'article_admin',
            'parent_id' => 30,
            'deep' => 2,
            'type' => 10,
            'is_system' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);
    }
}
