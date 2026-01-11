<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-17 15:16:01
 * @FilePath: \database\seeders\LaravelFastApi\Service\Level\LevelItemSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Service\Level;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levelItemData = [
            //1
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'type'=>10,'item_name'=>'用户积分','item_code'=>'user_score','description'=>'用户积分项']
        ];

        DB::table('level_item')->insert($levelItemData);
    }
}
