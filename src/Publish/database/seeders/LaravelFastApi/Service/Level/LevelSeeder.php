<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-17 16:48:33
 * @FilePath: \database\seeders\LaravelFastApi\Service\Level\LevelSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */


namespace Database\Seeders\LaravelFastApi\Service\Level;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userLevelData = [
            //1
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'生铁','level_code'=>'V0','amount'=>0,'background_id'=>0,'note'=>''],
            //2
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'青铜','level_code'=>'V1','amount'=>0,'background_id'=>0,'note'=>''],
            //3
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'白银','level_code'=>'V2','amount'=>0,'background_id'=>0,'note'=>''],
            //4
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'黄金','level_code'=>'V3','amount'=>0,'background_id'=>0,'note'=>''],
            //5
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'钻石','level_code'=>'V4','amount'=>0,'background_id'=>0,'note'=>''],
            //6
            ['created_at'=>\date('Y-m-d H:i:s',time()),'created_time'=>time(),'sort'=>100,'level_name'=>'皇冠','level_code'=>'V5','amount'=>0,'background_id'=>0,'note'=>''],
        ];

        DB::table('user_level')->insert($userLevelData);

    }
}
