<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-07 06:36:56
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 22:53:15
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\UserInfoSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserInfo;

class UserInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(UserInfo::class, function ($query) {
            $query->truncate();
        });

        $this->command->info('开始填用户详情');

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
            $this->command->warn('用户数据不完整，跳过 UserInfoSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $userInfoDataArray = [
            [
                'user_info_uid' => get_snow_flake_id(),
                'user_uid' => $develop_user_uid,
                'nick_name' => 'develop',
                'family_name' => '',
                'name' => '',
                'real_name' => '',
                'id_number' => null,
                'sex' => 0,
                'solar_birthday_at' => null,
                'solar_birthday_time' => 0,
                'chinese_birthday_at' => null,
                'chinese_birthday_time' => 0,
                'introduction' => 'I am a super developer',
            ],
            [
                'user_info_uid' => get_snow_flake_id(),
                'user_uid' => $super_user_uid,
                'nick_name' => 'super',
                'family_name' => '',
                'name' => '',
                'real_name' => '',
                'id_number' => null,
                'sex' => 10,
                'solar_birthday_at' => null,
                'solar_birthday_time' => 0,
                'chinese_birthday_at' => null,
                'chinese_birthday_time' => 0,
                'introduction' => 'I am a super administrator',
            ],
            [
                'user_info_uid' => get_snow_flake_id(),
                'user_uid' => $admin_user_uid,
                'nick_name' => 'admin',
                'family_name' => '',
                'name' => '',
                'real_name' => '',
                'id_number' => null,
                'sex' => 10,
                'solar_birthday_at' => null,
                'solar_birthday_time' => 0,
                'chinese_birthday_at' => null,
                'chinese_birthday_time' => 0,
                'introduction' => 'I am an administrator',
            ],
            [
                'user_info_uid' => get_snow_flake_id(),
                'user_uid' => $user_user_uid,
                'nick_name' => 'user',
                'family_name' => '',
                'name' => '',
                'real_name' => '',
                'id_number' => null,
                'sex' => 10,
                'solar_birthday_at' => null,
                'solar_birthday_time' => 0,
                'chinese_birthday_at' => null,
                'chinese_birthday_time' => 0,
                'introduction' => 'I am an user',
            ],
        ];


        // 用模型批量创建
        $insertResult = ShardHelperFacade::insertBatchWithShard(UserInfo::class, $userInfoDataArray);

        $this->command->info('用户详情插入结果：' . json_encode($insertResult, JSON_UNESCAPED_UNICODE));

        $this->command->info('✅填充用户详情完成');
    }
}
