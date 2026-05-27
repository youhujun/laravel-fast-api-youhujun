<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-24 14:49:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 22:11:02
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Admin\AdminSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShardHelperFacade::queryAllShards(Admin::class, function ($query) {
            $query->truncate();
        });

        $userCollection = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->select(['user_uid', 'account_name']);
            },
            'account_name',
            ['develop', 'super', 'admin']
        );

        $user_uid_map_array = [];

        foreach ($userCollection as $userObject) {
            $user_uid_map_array[$userObject->account_name] = $userObject->user_uid;
        }

        // 查询对应的 user_uid 和 admin_uid
        $develop_user_uid = $user_uid_map_array['develop'] ?? null;
        $super_user_uid = $user_uid_map_array['super'] ?? null;
        $admin_user_uid = $user_uid_map_array['admin'] ?? null;

        // 检查用户是否存在
        if (!$develop_user_uid || !$super_user_uid || !$admin_user_uid) {
            $this->command->warn('用户数据不完整，跳过 AdminSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->command->info('开始创建管理员数据...');


        Admin::bindShardBusinessId($develop_user_uid);

        // 开发者管理员
        Admin::create([
            'admin_uid' => get_snow_flake_id(),
            'user_uid' => $develop_user_uid,
            'account_name' => 'develop',
            'phone_area_code' => '+86',
            'password' => Hash::make(config('common.default_password'))
        ]);
        $this->command->info("开发者管理员创建完成");


        Admin::bindShardBusinessId($super_user_uid);

        // 超级管理员
        Admin::create([
            'admin_uid' => get_snow_flake_id(),
            'user_uid' => $super_user_uid,
            'account_name' => 'super',
            'phone_area_code' => '+86',
            'password' => Hash::make(config('common.default_password')),
        ]);
        $this->command->info("超级管理员创建完成");

        Admin::bindShardBusinessId($admin_user_uid);

        // 普通管理员
        Admin::create([
            'admin_uid' => get_snow_flake_id(),
            'user_uid' => $admin_user_uid,
            'account_name' => 'admin',
            'phone_area_code' => '+86',
            'password' => Hash::make(config('common.default_password'))
        ]);
        $this->command->info("普通管理员创建完成");

        $this->command->info('✅ 所有管理员数据填充完成！');
    }
}
