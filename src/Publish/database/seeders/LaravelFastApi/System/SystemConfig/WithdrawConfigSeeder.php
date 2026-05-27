<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 12:17:00
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-15 14:48:25
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\System\SystemConfig\WithdrawConfigSeeder.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

namespace Database\Seeders\LaravelFastApi\System\SystemConfig;

use App\Models\LaravelFastApi\V1\System\SystemConfig\WithdrawConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WithdrawConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WithdrawConfig::truncate();

        $this->command->info('开始填充系统提现配置');
        // 手续费
        WithdrawConfig::create([
            'item_name' => 'handing_fee_reason_amount',
            'item_value' => '500',
            'value_type' => 10,
            'sort' => 100,
            'note' => '提现手续费金额',
            'created_time' => time(),
        ]);

        WithdrawConfig::create([
            'item_name' => 'handing_fee_use_amount',
            'item_value' => '5',
            'value_type' => 20,
            'sort' => 100,
            'note' => '提现手续费比例',
            'created_time' => time(),
        ]);

        WithdrawConfig::create([
            'item_name' => 'handing_fee_use_percentage',
            'item_value' => '1',
            'value_type' => 20,
            'sort' => 100,
            'note' => '手续费使用比例',
            'created_time' => time(),
        ]);

        // 个税
        WithdrawConfig::create([
            'item_name' => 'income_tax_reason_amount',
            'item_value' => '5000',
            'value_type' => 10,
            'sort' => 100,
            'note' => '最大所得税金额',
            'created_time' => time(),
        ]);

        WithdrawConfig::create([
            'item_name' => 'income_tax_use_percentage',
            'item_value' => '2',
            'value_type' => 20,
            'sort' => 100,
            'note' => '最大所得税使用率',
            'created_time' => time(),
        ]);


        $this->command->info('✅填充系统提现配置数据完成');
    }
}
