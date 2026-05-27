<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-17 22:08:47
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-18 02:52:01
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\Role\UserCascaderSeeder.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User\Role;

use App\Facades\Common\V1\Shard\ShardHelperFacade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserCascader;
class UserCascaderSeeder extends Seeder
{
    /**
     * 基础表名（和迁移/模型模板对齐，仅作标识）
     */
    protected $baseTable = 'user_cascaders';

    /**
     * Run the database seeds.
     * 核心逻辑：保留Replace作为VSCode替换占位符，分片表用模型处理，非分片表用DB处理
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(UserCascader::class, function ($query) {
            $query->truncate();
        });

        // 控制台提示（增强可读性）
        $this->command->info("开始填充用户角色级联回显数据");
        
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
            $this->command->warn('用户数据不完整，跳过 UserCascaderSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $role_cascader_id_array = [[40]];

        $insertDataArray = [
            [
                'user_cascader_uid' => get_snow_flake_id(),
                'user_uid' => $develop_user_uid,
                'role_cascader_json' => json_encode($role_cascader_id_array),
                'revision' => 0,
            ],
            [
                'user_cascader_uid' => get_snow_flake_id(),
                'user_uid' => $super_user_uid,
                'role_cascader_json' => json_encode($role_cascader_id_array),
                'revision' => 0,
            ],
            [
                'user_cascader_uid' => get_snow_flake_id(),
                'user_uid' => $admin_user_uid,
                'role_cascader_json' => json_encode($role_cascader_id_array),
                'revision' => 0,
            ],
            [
                'user_cascader_uid' => get_snow_flake_id(),
                'user_uid' => $user_user_uid,
                'role_cascader_json' => json_encode($role_cascader_id_array),
                'revision' => 0,
            ],
        ];

        $insertResult = ShardHelperFacade::insertBatchWithShard(UserCascader::class, $insertDataArray);

        $this->command->info('用户角色级联回显插入结果：' . json_encode($insertResult, JSON_UNESCAPED_UNICODE));

        $this->command->info('✅填充用户角色级联回显完成');
    }
}