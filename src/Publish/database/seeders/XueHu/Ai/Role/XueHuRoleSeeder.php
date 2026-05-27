<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-29 01:14:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-29 01:52:14
 * @FilePath: \xue-hu-api-12\database\seeders\XueHu\Ai\Role\XueHuRoleSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\XueHu\Ai\Role;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\System\Role\Role;

class XueHuRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始创建雪鹄专属角色');


        Role::create([
            'id' => 50,
            'role_name' => '雪鹄主人',
            'logic_name' => 'xuehu_master',
            'parent_id' => 0,
            'deep' => 1,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);


        Role::create([
            'id' => 500,
            'role_name' => '房主',
            'logic_name' => 'xuehu_owner',
            'parent_id' => 50,
            'deep' => 2,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);


        Role::create([
            'id' => 600,
            'role_name' => '亲密伙伴',
            'logic_name' => 'xuehu_intimate',
            'parent_id' => 50,
            'deep' => 2,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);


        Role::create([
            'id' => 700,
            'role_name' => '访客',
            'logic_name' => 'xuehu_visitor',
            'parent_id' => 50,
            'deep' => 2,
            'switch' => 1,
            'created_time' => time(),
            'updated_time' => time(),
        ]);


        $this->command->info('✅雪鹄专属角色创建完成');
    }
}
