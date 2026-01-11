<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-23 17:42:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:19:56
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
		if (!Schema::connection($db_connection)->hasTable('admin_event_log')) 
		{
			Schema::connection($db_connection)->create('admin_event_log', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('admin_id')->default(0)->index()->comment('管理员id');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
	
				$table->unsignedInteger('event_type')->default(0)->index()->comment('事件类型');
				$table->string('event_route_action',128)->default('')->comment('事件路由');
				$table->string('event_name',64)->default('')->comment('事件名称');
				$table->string('event_code',64)->default('')->comment('事件编码');
				$table->text('note')->nullable()->comment('备注数据');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}admin_event_log` comment '管理员事件日志'");
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
		//注意是否需要修改mysql连接名和表名
		if (Schema::connection($db_connection)->hasTable('admin_event_log')) 
		{
			Schema::connection($db_connection)->dropIfExists('admin_event_log');
		}
        
    }
};
