<?php

/*
 * @Descripttion: 游鹄生态-用户填充文件（模型创建）
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-16 11:39:21
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-29 20:58:35
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\UserSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\LaravelFastApi\V1\User\User;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        ShardHelperFacade::queryAllShards(User::class, function ($query) {
            $query->truncate();
        });
        // ========== 基础配置：生成第一个用户的雪花ID（适配分片） ==========
        $one_user_uid = get_snow_flake_id();
        $this->command->info("📌 生成第一个用户UID：{$one_user_uid}");

        // ========== 1. 创建开发者用户（develop）==========
        $this->command->info("🔨 开始创建开发者用户...");

        User::bindShardBusinessId($one_user_uid);
        $developUser = User::create([
            'user_uid' => $one_user_uid,
            'source_user_uid' => 0,
            'parent_user_uid' => 0,
            'revision' => 0,
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 0,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => 'develop',
            'phone_area_code' => '+86',
            'phone' => null,
            'password' => Hash::make(config('common.default_password')),
            'email' => null,
        ]);

        $this->command->info("✅ 开发者用户创建完成，存储表：{$developUser->getTable()}");

        // ========== 2. 创建超级管理员（super）==========
        $this->command->info("🔨 开始创建超级管理员...");

        $two_user_uid = get_snow_flake_id();

        User::bindShardBusinessId($two_user_uid);

        $superUser = User::create([
            'user_uid' => $two_user_uid,
            'source_user_uid' => $one_user_uid,
            'parent_user_uid' => 0,
            'revision' => 0,
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 0,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => 'super',
            'phone_area_code' => '+86',
            'phone' => null,
            'password' => Hash::make(config('common.default_password')),
            'email' => null,
        ]);
        $this->command->info("✅ 超级管理员创建完成，UID：{$two_user_uid}，存储表：{$superUser->getTable()}");

        // ========== 3. 创建管理员（admin）==========
        $this->command->info("🔨 开始创建普通管理员...");
        $three_user_uid = get_snow_flake_id();

        User::bindShardBusinessId($three_user_uid);

        $adminUser = User::create([
            'user_uid' => $three_user_uid,
            'source_user_uid' => $two_user_uid,
            'parent_user_uid' => 0,
            'revision' => 0,
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 0,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => 'admin',
            'phone_area_code' => '+86',
            'phone' => null,
            'password' => Hash::make(config('common.default_password')),
            'email' => null,
        ]);

        $this->command->info("✅ 普通管理员创建完成，UID：{$three_user_uid}，存储表：{$adminUser->getTable()}");

        // ========== 4. 创建普通用户（user）==========
        $this->command->info("🔨 开始创建普通用户...");
        $four_user_uid = get_snow_flake_id();

        User::bindShardBusinessId($four_user_uid);

        $userObject = User::create([
            'user_uid' => $four_user_uid,
            'source_user_uid' => $three_user_uid,
            'parent_user_uid' => 0,
            'revision' => 0,
            'account_status' => 1,
            'real_auth_status' => 10,
            'level_id' => 0,
            'source' => 0,
            'remember_token' => null,
            'auth_token' => Str::uuid()->toString(),
            'account_name' => 'user',
            'phone_area_code' => '+86',
            'phone' => null,
            'password' => Hash::make(config('common.default_password')),
            'email' => null,
        ]);
        $this->command->info("✅ 普通用户创建完成，UID：{$four_user_uid}，存储表：{$userObject->getTable()}");

        // ========== 最终提示 ==========
        $this->command->info('🎉 所有用户数据填充完成（模型创建+分库分表适配）！');
        $this->command->info("📋 填充的用户UID列表：
            - 开发者：{$one_user_uid}
            - 超级管理员：{$two_user_uid}
            - 普通管理员：{$three_user_uid}
            - 普通用户：{$four_user_uid}
        ");

        $this->command->info('✅ 用户数据填充完成（模型创建）！');
    }
}
