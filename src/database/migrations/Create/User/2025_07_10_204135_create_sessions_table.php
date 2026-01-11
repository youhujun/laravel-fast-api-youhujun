<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-10 20:41:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 20:50:02
 * @FilePath: \database\migrations\2025_07_10_204135_create_sessions_table.php
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
     *
     * @return void
     */
    public function up()
    {
		$db_connection = config('youhujun.db_connection');
		//注意是否需要修改mysql连接名和表名
		if (!Schema::connection($db_connection)->hasTable('sessions')) 
		{
			Schema::connection($db_connection)->create('sessions', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('user_id')->defalut(0)->index()->comment('用户id');
				$table->string('ip_address', 45)->nullable()->comment('ip地址');
				$table->text('user_agent')->nullable()->comment('用户代理');
				$table->longText('payload')->comment('载荷');
				$table->integer('last_activity')->index()->comment('最后访问');
				$table->string('note',128)->default('')->comment('备注');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
			});

			//注意是否需要修改mysql连接名
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}sessions` comment '用户sessions表'");
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

		if (Schema::connection($db_connection)->hasTable('sessions')) 
		{
			 Schema::connection($db_connection)->dropIfExists('sessions');
		}
       
    }
};
