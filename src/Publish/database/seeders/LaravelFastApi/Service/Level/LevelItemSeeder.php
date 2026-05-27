<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 04:23:03
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Service\Level\LevelItemSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Service\Level;

use App\Models\LaravelFastApi\V1\System\Level\LevelItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LevelItem::truncate();
		
        $this->command->info('开始填充系统级别配置项');

        LevelItem::create([
            'id' => 10,
            'type' => 10,
            'item_name' => '用户积分',
            'item_code' => 'user_score',
            'description' => '用户积分项',
            'sort' => 100,
            'created_time' => time(),
        ]);


        $this->command->info('✅填充系统级别配置项完成');
    }
}
