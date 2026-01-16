<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2026-01-16 00:38:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:53:58
 * @FilePath: \src\database\migrations\Create\System\Config\2026_01_16_003815_create_invite_code_sequences_table.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$db_connection = config('youhujun.db_connection');
		//注意是否需要修改mysql连接名和表名
		if (!Schema::connection($db_connection)->hasTable('invite_code_sequences')) 
		{
			Schema::connection($db_connection)->create('invite_code_sequences', function (Blueprint $table)
			{
				$table->id()->comment('自增序列ID(邀请码兜底用)');
				// 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
			});

			//注意是否需要修改mysql连接名
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}invite_code_sequences` comment '邀请码序列表'");
		}
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		$db_connection = config('youhujun.db_connection');

		if (Schema::connection($db_connection)->hasTable('invite_code_sequences')) 
		{
			 Schema::connection($db_connection)->dropIfExists('invite_code_sequences');
		}
       
    }
};
