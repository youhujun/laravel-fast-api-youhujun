<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 04:23:58
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Service\Level\LevelItemUnionSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Service\Level;

use App\Models\LaravelFastApi\V1\System\Level\Union\UserLevelItemUnion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelItemUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserLevelItemUnion::truncate();
		
        $this->command->info('开始绑定用户等级与等级项');

        // 生铁(V0), user_level_id=10, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 10,
            'level_item_id' => 10,
            'value' => 0,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);

        // 青铜(V1), user_level_id=20, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 20,
            'level_item_id' => 10,
            'value' => 100,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);

        // 白银(V2), user_level_id=30, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 30,
            'level_item_id' => 10,
            'value' => 600,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);

        // 黄金(V3), user_level_id=40, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 40,
            'level_item_id' => 10,
            'value' => 1800,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);

        // 钻石(V4), user_level_id=50, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 50,
            'level_item_id' => 10,
            'value' => 7200,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);

        // 皇冠(V5), user_level_id=60, level_item_id=10
        UserLevelItemUnion::create([
            'user_level_id' => 60,
            'level_item_id' => 10,
            'value' => 16000,
            'value_type' => 40,
            'sort' => 100,
            'created_time' => time(),
        ]);


        $this->command->info('✅绑定用户等级与等级项完成');
    }
}
