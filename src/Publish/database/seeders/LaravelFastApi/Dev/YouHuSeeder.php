<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-30 01:07:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-30 01:08:44
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Dev\YouHuSeeder.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Dev;

use App\Facades\Common\V1\Shard\ShardHelperFacade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Database\Seeders\YouHu\System\SystemConfig\SystemConfigYouHuSeeder;
use Database\Seeders\YouHu\System\SystemConfig\SystemWecahtConfigYouHuSeeder;

class YouHuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 核心逻辑：保留Replace作为VSCode替换占位符，分片表用模型处理，非分片表用DB处理
     */
    public function run(): void
    {
        $this->call([
            //系统配置
            SystemConfigYouHuSeeder::class,
            //微信配置
            SystemWecahtConfigYouHuSeeder::class

        ]);
    }
}
