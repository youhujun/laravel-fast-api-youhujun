<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-19 16:08:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-28 23:24:02
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\UserRoleUnionSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\System\Role\Role;
use App\Models\LaravelFastApi\V1\User\Union\UserRoleUnion;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class UserRoleUnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShardHelperFacade::queryAllShards(UserRoleUnion::class, function ($query) {
            $query->truncate();
        });

        $this->command->info('开始创建用户角色关联数据...');

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
            $this->command->warn('用户数据不完整，跳过 UserRoleUnionSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        // 先获取所有管理员的角色 ID
        $adminRoleCollection = Role::select(['id'])->where('type', 10)->orderBy('id', 'asc')->get();

        // 获取 admin 和 user 角色的 ID
        $admin_role_id = Role::where('logic_name', 'admin')->where('type', 10)->value('id');
        $user_role_id = Role::where('logic_name', 'user')->where('type', 20)->value('id');

        // 开发者角色数据 - 为开发者分配所有角色
        foreach ($adminRoleCollection as $role) {
            UserRoleUnion::bindShardBusinessId($develop_user_uid);
            UserRoleUnion::create([
                'user_role_union_uid' => get_snow_flake_id(),
                'user_uid' => $develop_user_uid,
                'role_id' => $role->id,
                'type' => 10,
            ]);
        }
        UserRoleUnion::bindShardBusinessId($develop_user_uid);
        UserRoleUnion::create([
            'user_role_union_uid' => get_snow_flake_id(),
            'user_uid' => $develop_user_uid,
            'role_id' => $user_role_id,
            'type' => 20,
        ]);

        $this->command->info("开发者角色关联创建完成,用户UID：{$develop_user_uid}");

        // 超级管理员角色数据 - 为超级管理员分配所有角色
        foreach ($adminRoleCollection as $role) {
            UserRoleUnion::bindShardBusinessId($super_user_uid);

            UserRoleUnion::create([
                'user_role_union_uid' => get_snow_flake_id(),
                'user_uid' => $super_user_uid,
                'role_id' => $role->id,
                'type' => 10,
            ]);
        }

        UserRoleUnion::bindShardBusinessId($super_user_uid);

        UserRoleUnion::create([
            'user_role_union_uid' => get_snow_flake_id(),
            'user_uid' => $super_user_uid,
            'role_id' => $user_role_id,
            'type' => 20,
        ]);

        $this->command->info("超级管理员角色关联创建完成,用户UID：{$super_user_uid}");


        UserRoleUnion::bindShardBusinessId($admin_user_uid);

        // admin 用户角色数据 - admin管理员同时有admin和user角色
        UserRoleUnion::create([
            'user_role_union_uid' => get_snow_flake_id(),
            'user_uid' => $admin_user_uid,
            'role_id' => $admin_role_id,
            'type' => 10,
        ]);

        UserRoleUnion::create([
            'user_role_union_uid' => get_snow_flake_id(),
            'user_uid' => $admin_user_uid,
            'role_id' => $user_role_id,
            'type' => 20,
        ]);

        $this->command->info("普通管理员角色关联创建完成,用户UID：{$admin_user_uid}");


        UserRoleUnion::bindShardBusinessId($user_user_uid);

        // user 用户角色数据 - 普通用户只有user角色
        UserRoleUnion::create([
            'user_role_union_uid' => get_snow_flake_id(),
            'user_uid' => $user_user_uid,
            'role_id' => $user_role_id,
            'type' => 20,

        ]);
        $this->command->info("普通用户角色关联创建完成,用户UID：{$user_user_uid}");

        $this->command->info('✅ 所有用户角色关联数据填充完成！');
    }
}
