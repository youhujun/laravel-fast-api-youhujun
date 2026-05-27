<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 02:33:45
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\System\SystemConfig\VoiceConfigSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\System\SystemConfig;

use App\Models\LaravelFastApi\V1\System\SystemConfig\SystemVoiceConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoiceConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemVoiceConfig::truncate();

        $this->command->info('开始填充系统提示音配置');

        SystemVoiceConfig::create([
            'voice_title' => '测试提示',
            'channle_name' => 'admin_test',
            'channle_event' => 'AdminTest',
            'note' => '后台测试认证',
            'voice_save_type' => 20,
            'voice_url' => 'https://visit.youhujun.com/qiniu.youhujun.com/config/file/voice/test_notice.mp3',
            'sort' => 100,
            'created_time' => time(),
            'updated_time' => time(),
        ]);

        SystemVoiceConfig::create([
            'voice_title' => '实名认证提示',
            'channle_name' => 'admin_real_auth_apply',
            'channle_event' => 'RealAuthApply',
            'note' => '提示用户申请实名认证',
            'voice_save_type' => 20,
            'voice_url' => 'https://visit.youhujun.com/qiniu.youhujun.com/config/file/voice/new_real_auth.mp3',
            'sort' => 100,
            'created_time' => time(),
            'updated_time' => time(),
        ]);


        $this->command->info('✅填充系统提示音配置数据完成');
    }
}
