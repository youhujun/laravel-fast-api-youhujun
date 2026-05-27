<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-03-24 16:25:12
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-05 13:14:09
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\Auth\YouhuAuthServiceSeeder.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\Auth;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Facades\Common\V1\Shard\ShardHelperFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Sign\KeyManagerFacade;
use YouHuJun\Tool\App\Facades\V1\Utils\Secret\AESFacade;
use App\Models\LaravelFastApi\V1\Api\Auth\YouHuAuthService;
use App\Models\LaravelFastApi\V1\User\User;

class YouhuAuthServiceSeeder extends Seeder
{
    /**
     * 基础表名（和迁移/模型模板对齐，仅作标识）
     */
    protected $baseTable = 'youhu_auth_services';

    /**
     * Run the database seeds.
     *
     */
    public function run(): void
    {
        ShardHelperFacade::queryAllShards(YouHuAuthService::class, function ($query) {
            $query->truncate();
        });

        $userCollection = ShardHelperFacade::queryAllShards(
            User::class,
            function ($query) {
                $query->where('account_status', 1);
            },
            'account_name',
            ['develop','super','admin','user']
        );

        $userUidCollection = $userCollection->pluck('biz_id');

        // 控制台提示（增强可读性）
        $this->command->info("开始填充【{$this->baseTable}】表数据...");

        $userUidCollection->map(function ($user_uid) {
            $authToken = Str::uuid()->toString();

            $secretKey = KeyManagerFacade::generateSecureSecretKey(40, ['letters_upper', 'letters_lower', 'numbers']);

            $aesKey = config('common.aes.key');

            $encryptedSecretKey = AESFacade::encrypt($secretKey, $aesKey);

            $encryptedAuthToken = AESFacade::encrypt($authToken, $aesKey);

            $authDataArray = [
                'user_uid' => $user_uid,
                'secret_key' => $encryptedSecretKey,
                'auth_token' => $encryptedAuthToken,
                'status' => 1,
                'service_flag' => 'youhu-base',
                'created_time' => time(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $createResult = ShardHelperFacade::createWithShard(YouHuAuthService::class, $user_uid, $authDataArray);
        });

        // 完成提示
        $this->command->info('✅【'.$this->baseTable.'】表数据填充完成！');
    }
}
