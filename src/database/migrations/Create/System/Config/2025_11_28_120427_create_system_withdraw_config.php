<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 12:04:27
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-28 12:25:51
 * @FilePath: d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api\src\database\migrations\Create\System\Config\2025_11_28_120427_create_system_withdraw_config.php
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
		if (!Schema::connection($db_connection)->hasTable('system_withdraw_config')) 
		{
			Schema::connection($db_connection)->create('system_withdraw_config', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->unsignedBigInteger('admin_id')->default(0)->comment('管理员id');
				$table->string('item_name',32)->unique()->nullable()->comment('字段名称 唯一');
				$table->string('item_value',32)->default('')->comment('字段值');
				$table->unsignedTinyInteger('value_type')->index()->default(10)->comment('值得类型 10整数 20小数');
				$table->string('note',128)->default('')->comment('备注');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
			});

			//注意是否需要修改mysql连接名
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}system_withdraw_config` comment '系统提现配置表'");
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

		if (Schema::connection($db_connection)->hasTable('system_withdraw_config')) 
		{
			 Schema::connection($db_connection)->dropIfExists('system_withdraw_config');
		}
       
    }
};
