<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-12-03 10:18:50
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 04:33:55
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\UserAmountSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAmount;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class UserAmountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(UserAmount::class, function ($query) {
            $query->truncate();
        });

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
            $this->command->warn('用户数据不完整，跳过 UserAmountSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->command->info('开始创建用户余额数据...');

        UserAmount::bindShardBusinessId($develop_user_uid);
        // 开发者余额
        UserAmount::create([
            'user_amount_uid' => get_snow_flake_id(),
            'user_uid' => $develop_user_uid,
            'revision' => 0,
            'amount' => 0.00,
            'bonus' => 0.00,
            'prepare_bonus' => 0.00,
            'coin' => 0.00,
            'score' => 0,
            'note' => '初始余额',
            'sort' => 0,
        ]);

        $this->command->info("开发者余额创建完成");

        UserAmount::bindShardBusinessId($super_user_uid);

        // 超级管理员余额
        UserAmount::create([
            'user_amount_uid' => get_snow_flake_id(),
            'user_uid' => $super_user_uid,
            'revision' => 0,
            'amount' => 0.00,
            'bonus' => 0.00,
            'prepare_bonus' => 0.00,
            'coin' => 0.00,
            'score' => 0,
            'note' => '初始余额',
            'sort' => 0,
        ]);
        $this->command->info("超级管理员余额创建完成");


        UserAmount::bindShardBusinessId($admin_user_uid);

        // 普通管理员余额
        UserAmount::create([
            'user_amount_uid' => get_snow_flake_id(),
            'user_uid' => $admin_user_uid,
            'revision' => 0,
            'amount' => 0.00,
            'bonus' => 0.00,
            'prepare_bonus' => 0.00,
            'coin' => 0.00,
            'score' => 0,
            'note' => '初始余额',
            'sort' => 0,
        ]);
        $this->command->info("普通管理员余额创建完成");


        UserAmount::bindShardBusinessId($user_user_uid);

        // 普通用户余额
        UserAmount::create([
            'user_amount_uid' => get_snow_flake_id(),
            'user_uid' => $user_user_uid,
            'revision' => 0,
            'amount' => 0.00,
            'bonus' => 0.00,
            'prepare_bonus' => 0.00,
            'coin' => 0.00,
            'score' => 0,
            'note' => '初始余额',
            'sort' => 0,
        ]);
        $this->command->info("普通用户余额创建完成");

        $this->command->info('✅ 所有用户余额数据填充完成！');
    }
}
