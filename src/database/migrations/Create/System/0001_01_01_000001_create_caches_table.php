<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-10 15:23:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 20:11:37
 * @FilePath: \database\migrations\bak\0001_01_01_000001_create_cache_table.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
		$db_connection = config('youhujun.db_connection');

		if (!Schema::connection($db_connection)->hasTable('cache')) 
		{
			Schema::connection($db_connection)->create('cache', function (Blueprint $table) {
				$table->string('key')->primary()->comment('主键key');
				$table->mediumText('value')->comment('cache锁值');
				$table->integer('expiration')->comment('有效期单位分钟');

				// 索引
				$table->index('expiration');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}cache` comment 'cache表'");
		}

		if (!Schema::connection($db_connection)->hasTable('cache_locks')) 
		{
			Schema::connection($db_connection)->create('cache_locks', function (Blueprint $table)
			{
				$table->string('key')->primary()->comment('主键key');
				$table->string('owner')->comment('所有者');
				$table->integer('expiration')->comment('有效期单位分钟');

				// 索引
				$table->index('owner');
				$table->index('expiration');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}cache_locks` comment 'cache_locks表'");
		}

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		$db_connection = config('youhujun.db_connection');
		if (Schema::connection($db_connection)->hasTable('cache')) 
		{
			Schema::dropIfExists('cache');
		}

		if (Schema::connection($db_connection)->hasTable('cache_locks')) 
		{
			Schema::dropIfExists('cache_locks');
		} 
    }
};
