<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-02-07 06:36:17
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-10 22:53:42
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\User\UserAvatarSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaravelFastApi\V1\User\User;
use App\Models\LaravelFastApi\V1\User\Info\UserAvatar;
use App\Models\LaravelFastApi\V1\Picture\AlbumPicture;
use App\Facades\Common\V1\Shard\ShardHelperFacade;

class UserAvatarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(UserAvatar::class, function ($query) {
            $query->truncate();
        });

        $this->command->info('开始填充用户头像');

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
            $this->command->warn('用户数据不完整，跳过 UserAvatarSeeder');
            $this->command->info('用户UID状态：' . json_encode($user_uid_map_array, JSON_UNESCAPED_UNICODE));
            return;
        }

        $albumObject = ShardHelperFacade::queryAllShards(
            AlbumPicture::class,
            function ($query) {
                $query->where('picture_tag', 'avatar');
            },
            'picture_tag',
            ['avatar']
        )->first();

        if (!$albumObject) {
            $this->command->warn('头像不完整，跳过 AlbumSeeder');
            return;
        }

        $album_pcicture_uid = $albumObject->album_picture_uid;

        // ========== 2. 创建用户头像（用模型创建，自动触发关联逻辑） ==========
        $avatarDataArray = [
            [
                'user_avatar_uid' => get_snow_flake_id(),
                'user_uid' => $develop_user_uid,
                'album_picture_uid' => $album_pcicture_uid,
                'revision' => 0,
                'is_default' => 1,
            ],
            [
                'user_avatar_uid' => get_snow_flake_id(),
                'user_uid' => $super_user_uid,
                'album_picture_uid' => $album_pcicture_uid,
                'revision' => 0,
                'is_default' => 1,
            ],
            [
                'user_avatar_uid' => get_snow_flake_id(),
                'user_uid' => $admin_user_uid,
                'album_picture_uid' => $album_pcicture_uid,
                'revision' => 0,
                'is_default' => 1,
            ],
            [
                'user_avatar_uid' => get_snow_flake_id(),
                'user_uid' => $user_user_uid,
                'album_picture_uid' => $album_pcicture_uid,
                'revision' => 0,
                'is_default' => 1,
            ],
        ];
        $insertResult = ShardHelperFacade::insertBatchWithShard(UserAvatar::class, $avatarDataArray);

        $this->command->info('用户户头像插入结果：' . json_encode($insertResult, JSON_UNESCAPED_UNICODE));

        $this->command->info('✅填充用户头像完成');
    }
}
