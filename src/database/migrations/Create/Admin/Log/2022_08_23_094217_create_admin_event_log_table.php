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
		if (!Schema::connection($db_connection)->hasTable('admin_event_logs'))
		{
			Schema::connection($db_connection)->create('admin_event_logs', function (Blueprint $table) {

				$table->char('admin_event_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
				$table->char('admin_uid', 20)->notNull()->default('')->comment('管理员uid,雪花ID');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

				$table->unsignedInteger('event_type')->notNull()->default(0)->comment('事件类型');
				$table->string('event_route_action',128)->notNull()->default('')->comment('事件路由');
				$table->string('event_name',64)->notNull()->default('')->comment('事件名称');
				$table->string('event_code',64)->notNull()->default('')->comment('事件编码');
				$table->text('note')->nullable()->comment('备注数据');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				// 索引
				$table->unique('admin_event_log_uid');
				$table->index('admin_uid');
				$table->index('event_type');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}admin_event_logs` comment '管理员事件日志'");
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
		if (Schema::connection($db_connection)->hasTable('admin_event_logs'))
		{
			Schema::connection($db_connection)->dropIfExists('admin_event_logs');
		}
        
    }
};
