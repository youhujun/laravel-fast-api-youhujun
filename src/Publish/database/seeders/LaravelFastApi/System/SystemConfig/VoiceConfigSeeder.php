<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-02-06 09:23:25
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-03-05 11:14:37
 * @FilePath: \database\seeders\LaravelFastApi\System\SystemConfig\VoiceConfigSeeder.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\System\SystemConfig;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoiceConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $voiceConfigData = [
			['admin_id' =>1,'voice_title'=>'测试提示','channle_name'=>'admin_test','channle_event'=>'AdminTest','note'=>'后台测试认证','voice_save_type'=>20,'voice_url'=>'https://qiniu.youhujun.com/config/file/voice/test_notice.mp3','created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
			['admin_id' =>1,'voice_title'=>'实名认证提示','channle_name'=>'admin_real_auth_apply','channle_event'=>'RealAuthApply','note'=>'提示用户申请实名认证','voice_save_type'=>20,'voice_url'=>'https://qiniu.youhujun.com/config/file/voice/new_real_auth.mp3','created_time'=>time(),'created_at'=>date('Y-m-d H:i:s',time()),'sort'=>100],
		];

		DB::table('system_voice_config')->insert($voiceConfigData);
    }
}
