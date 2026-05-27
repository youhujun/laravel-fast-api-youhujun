<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 11:55:20
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Service\Level\UserLevelSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Service\Level;

use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserLevel::truncate();
        $this->command->info('开始填充用户级别');

        UserLevel::create([
            'id' => 10,
            'level_name' => '青铜',
            'level_code' => 'V0',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        UserLevel::create([
            'id' => 20,
            'level_name' => '白银',
            'level_code' => 'V1',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        UserLevel::create([
            'id' => 30,
            'level_name' => '黄金',
            'level_code' => 'V2',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        UserLevel::create([
            'id' => 40,
            'level_name' => '钻石',
            'level_code' => 'V3',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        UserLevel::create([
            'id' => 50,
            'level_name' => '皇冠',
            'level_code' => 'V4',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);

        UserLevel::create([
            'id' => 60,
            'level_name' => '至尊',
            'level_code' => 'V5',
            'amount' => 0,
            'background_picture_uid' => '',
            'note' => '',
            'sort' => 100,
            'created_time' => time(),
        ]);


        $this->command->info('✅填充用户级别完成');
    }
}
