<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-24 14:49:06
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 12:21:05
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Picture\AlbumSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\Picture;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\Picture\Album;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use Illuminate\Support\Facades\Config;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShardHelperFacade::queryAllShards(Album::class, function ($query) {
            $query->truncate();
        });

        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

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
            $this->command->warn('用户数据不完整，跳过 AlbumSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }


        $adminCollection = ShardHelperFacade::queryAllShards(
            Admin::class,
            function ($query) {
                $query->select(['admin_uid','user_uid', 'account_name']);
            },
            'account_name',
            ['develop', 'super', 'admin']
        );

        $admin_uid_map_array = [];
        $admin_user_uid_map_array = [];

        foreach ($adminCollection as $adminObject) {
            $admin_uid_map_array[$adminObject->account_name] = $adminObject->admin_uid;
            $admin_user_uid_map_array[$adminObject->account_name] = $adminObject->user_uid;
        }

        $develop_admin_uid = $admin_uid_map_array['develop'] ?? null;
        $super_admin_uid = $admin_uid_map_array['super'] ?? null;
        $admin_admin_uid = $admin_uid_map_array['admin'] ?? null;

        $develop_admin_user_uid = $admin_user_uid_map_array['develop'] ?? null;
        $super_admin_user_uid = $admin_user_uid_map_array['super'] ?? null;
        $admin_admin_user_uid = $admin_user_uid_map_array['admin'] ?? null;

        // 检查管理员是否存在
        if (!$develop_admin_uid || !$super_admin_uid || !$admin_admin_uid) {
            $this->command->warn('管理员数据不完整，跳过 AlbumSeeder');
            $this->command->info('管理员UID状态：' . json_encode($admin_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        if (!$develop_admin_user_uid || !$super_admin_user_uid || !$admin_admin_user_uid) {
            $this->command->warn('管理员数据不完整，跳过 AlbumSeeder');
            $this->command->info('管理员用户UID状态：' . json_encode($admin_user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $this->command->info('开始创建相册数据...');


        Album::bindShardBusinessId($develop_user_uid);

        // 0 - 系统相册
        Album::create([
            'album_uid' => get_snow_flake_id(),
            'album_name' => 'config',
            'album_type' => 0,
            'user_uid' => $develop_user_uid,
            'is_default' => 1,
            'is_system' => 1
        ]);

        $this->command->info("系统相册创建完成");

        //用户相册
        Album::bindShardBusinessId($develop_admin_user_uid);

        // 10 - 管理员相册
        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'admin_uid' => $develop_admin_uid,
            'album_name' => 'admin_develop',
            'album_type' => 10,
            'user_uid' => $develop_admin_user_uid,
            'is_default' => 1
        ]);

        Album::bindShardBusinessId($super_admin_user_uid);

        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'admin_uid' => $super_admin_uid,
            'album_name' => 'admin_super',
            'album_type' => 10,
            'user_uid' => $super_admin_user_uid,
            'is_default' => 1
        ]);


        Album::bindShardBusinessId($admin_admin_user_uid);

        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'admin_uid' => $admin_admin_uid,
            'album_name' => 'admin_admin',
            'album_type' => 10,
            'user_uid' => $admin_admin_user_uid,
            'is_default' => 1
        ]);

        $this->command->info("管理员相册创建完成");


        Album::bindShardBusinessId($develop_user_uid);

        // 20 - 用户相册
        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'user_uid' => $develop_user_uid,
            'album_name' => 'user_develop',
            'album_type' => 20,
            'revision' => 0,
            'is_default' => 1
        ]);

        Album::bindShardBusinessId($super_user_uid);

        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'user_uid' => $super_user_uid,
            'album_name' => 'user_super',
            'album_type' => 20,
            'revision' => 0,
            'is_default' => 1
        ]);

        Album::bindShardBusinessId($admin_user_uid);

        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'user_uid' => $admin_user_uid,
            'album_name' => 'user_admin',
            'album_type' => 20,
            'revision' => 0,
            'is_default' => 1
        ]);

        Album::bindShardBusinessId($user_user_uid);

        Album::create([
            'album_uid' =>  get_snow_flake_id(),
            'user_uid' => $user_user_uid,
            'album_name' => 'user_user',
            'album_type' => 20,
            'revision' => 0,
            'is_default' => 1
        ]);
        $this->command->info("用户相册创建完成");

        $this->command->info('✅ 所有相册数据填充完成！');
    }
}
