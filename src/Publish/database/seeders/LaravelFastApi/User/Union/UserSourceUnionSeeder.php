<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-04-10 21:55:58
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 22:42:07
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\Union\UserSourceUnionSeeder.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User\Union;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Union\UserSourceUnion;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class UserSourceUnionSeeder extends Seeder
{
    /**
     * 基础表名（和迁移/模型模板对齐，仅作标识）
     */
    protected $baseTable = 'user_source_unions';

    /**
     * Run the database seeds.
     * 核心逻辑：保留Replace作为VSCode替换占位符，分片表用模型处理，非分片表用DB处理
     */
    public function run(): void
    {
        // 基础配置（和迁移/模型模板统一
        ShardHelperFacade::queryAllShards(UserSourceUnion::class, function ($query) {
            $query->truncate();
        });
        // 控制台提示（增强可读性）
        $this->command->info("开始填充【{$this->baseTable}】表数据...");

        $userCollection = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->select(['user_uid', 'account_name']);
            },
            'account_name',
            ['develop', 'super', 'admin', 'user']
        );

        $user_uid_map_array = [];

        foreach ($userCollection as $userObject) {
            $user_uid_map_array[$userObject->account_name] = $userObject->user_uid;
        }

        // 查询对应的 user_uid 和 admin_uid
        $develop_user_uid = $user_uid_map_array['develop'] ?? null;
        $super_user_uid = $user_uid_map_array['super'] ?? null;
        $admin_user_uid = $user_uid_map_array['admin'] ?? null;
        $user_user_uid = $user_uid_map_array['user'] ?? null;

        // 检查用户是否存在
        if (!$develop_user_uid || !$super_user_uid || !$admin_user_uid || !$user_user_uid) {
            $this->command->warn('用户数据不完整，跳过 UserSourceUnionSeeder');
            return;
        }

        $batchDataArray = [
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => $develop_user_uid,'first_uid' => 0,'second_uid' => 0,'sort' => 100],
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => $super_user_uid,'first_uid' => $develop_user_uid,'second_uid' => 0,'sort' => 100],
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => $admin_user_uid,'first_uid' => $super_user_uid,'second_uid' => $develop_user_uid,'sort' => 100],
            ['user_source_union_uid' => get_snow_flake_id(),'user_uid' => $user_user_uid,'first_uid' => $admin_user_uid,'second_uid' => $super_user_uid,'sort' => 100],
        ];

        ShardHelperFacade::insertBatchWithShard(UserSourceUnion::class, $batchDataArray);

        // 完成提示
        $this->command->info('✅【'.$this->baseTable.'】表数据填充完成！');
    }
}
