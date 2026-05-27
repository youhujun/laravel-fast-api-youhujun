<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-02-14 18:18:11
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-06 18:29:57
 * @FilePath: \youhu-laravel-api-12\database\seeders\LaravelFastApi\System\BankSeeder.php
 */

namespace Database\Seeders\LaravelFastApi\System;

use App\Models\LaravelFastApi\V1\System\Module\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::truncate();
        $this->command->info('开始填充银行');
        

        Bank::create([
            'bank_name' => '中国银行',
            'is_default' => 1,
            'sort' => 100,
            'created_time' => time(),
        ]);

        Bank::create([
            'bank_name' => '农业银行',
            'is_default' => 1,
            'sort' => 100,
            'created_time' => time(),
        ]);

        Bank::create([
            'bank_name' => '工商银行',
            'is_default' => 1,
            'sort' => 100,
            'created_time' => time(),
        ]);

        Bank::create([
            'bank_name' => '建设银行',
            'is_default' => 1,
            'sort' => 100,
            'created_time' => time(),
        ]);

		$this->command->info('✅填充银行完成');
    }
}
