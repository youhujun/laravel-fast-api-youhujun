<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-17 22:09:55
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-18 01:39:16
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Admin\Role\AdminCascaderSeeder.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Admin\Role;

use App\Facades\Common\V1\Shard\ShardHelperFacade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use App\Models\LaravelFastApi\V1\Admin\Info\AdminCascader;

class AdminCascaderSeeder extends Seeder
{
    /**
     * 基础表名（和迁移/模型模板对齐，仅作标识）
     */
    protected $baseTable = '';

    /**
     * Run the database seeds.
     * 核心逻辑：保留Replace作为VSCode替换占位符，分片表用模型处理，非分片表用DB处理
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(AdminCascader::class, function ($query) {
            $query->truncate();
        });

        // 控制台提示（增强可读性）
        $this->command->info("开始填充管理员角色级联回显数据");
        
        $adminCollection = ShardHelperFacade::queryAllShards(
            Admin::class,
            function ($query) {
                $query->select(['admin_uid', 'account_name']);
            },
            'account_name',
            ['develop', 'super', 'admin',]
        );

        $admin_uid_map_array = [];

        foreach ($adminCollection as $adminObject) {
            $admin_uid_map_array[$adminObject->account_name] = $adminObject->admin_uid;
        }

        // 查询对应的 user_uid 和 admin_uid
        $develop_admin_uid = $admin_uid_map_array['develop'] ?? null;
        $super_admin_uid = $admin_uid_map_array['super'] ?? null;
        $admin_admin_uid = $admin_uid_map_array['admin'] ?? null;

        // 检查用户是否存在
        if (!$develop_admin_uid || !$super_admin_uid || !$admin_admin_uid ) {
            $this->command->warn('管理员数据不完整，跳过 AdminCascaderSeeder');
            $this->command->info('管理员UID状态：' . json_encode($admin_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $super_role_cascader_id_array = [ [10], [20], [30], [30, 100], [30, 200], [30, 300], [30, 400] ];

        $admin_role_cascader_id_array = [[30], [30, 100], [30, 200], [30, 300], [30, 400]];

        $insertDataArray = [
            [
                'admin_cascader_uid' => get_snow_flake_id(),
                'admin_uid' => $develop_admin_uid,
                'role_cascader_json' => json_encode($super_role_cascader_id_array),
                'revision' => 0,
            ],
            [
                'admin_cascader_uid' => get_snow_flake_id(),
                'admin_uid' => $super_admin_uid,
                'role_cascader_json' => json_encode($super_role_cascader_id_array),
                'revision' => 0,
            ],
            [
                'admin_cascader_uid' => get_snow_flake_id(),
                'admin_uid' => $admin_admin_uid,
                'role_cascader_json' => json_encode($admin_role_cascader_id_array),
                'revision' => 0,
            ],
        ];

        $insertResult = ShardHelperFacade::insertBatchWithShard(AdminCascader::class, $insertDataArray);

        $this->command->info('管理员角色级联回显插入结果：' . json_encode($insertResult, JSON_UNESCAPED_UNICODE));

        $this->command->info('✅填充管理员角色级联回显完成');
    }
}