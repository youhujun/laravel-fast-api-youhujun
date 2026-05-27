<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-06 19:00:49
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 02:40:20
 * @FilePath: \youhu-laravel-api-12\database\seeders\TruncateSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use YouHuJun\Tool\App\Facades\V1\Utils\Snowflake\SnowflakeFacade;
use App\Models\LaravelFastApi\V1\System\SystemConfig;
use App\Models\LaravelFastApi\V1\System\SystemConfig\SystemVoiceConfig;
use App\Models\LaravelFastApi\V1\System\SystemConfig\WithdrawConfig;
use App\Models\LaravelFastApi\V1\System\Union\RolePermissionUnion;
use App\Models\LaravelFastApi\V1\System\Module\Article\Category;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\System\Level\LevelItem;
use App\Models\LaravelFastApi\V1\System\Level\UserLevel;
use App\Models\LaravelFastApi\V1\System\Level\Union\UserLevelItemUnion;
use App\Models\LaravelFastApi\V1\System\Module\Bank;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;
use App\Models\LaravelFastApi\V1\Admin\Admin;

class TruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('开始清理填充数据');


        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];


        SystemConfig::truncate();
        SystemVoiceConfig::truncate();
        WithdrawConfig::truncate();
        DB::connection($dbConnection)->table('permissions')->truncate();
        Role::truncate();
        RolePermissionUnion::truncate();
        Category::truncate();
        LevelItem::truncate();
        UserLevel::truncate();
        UserLevelItemUnion::truncate();
        DB::connection($dbConnection)->table('regions')->truncate();
        Bank::truncate();

        Album::truncate();
        AlbumPicture::truncate();

        User::truncate();
        UserInfo::truncate();
        UserAvatar::truncate();
        UserRoleUnion::truncate();
        UserAmount::truncate();
        Admin::truncate();



        $this->command->info('✅填充数据清理完成');
    }
}
