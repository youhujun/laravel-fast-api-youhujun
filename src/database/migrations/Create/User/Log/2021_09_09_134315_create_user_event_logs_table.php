<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-09 13:43:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 19:03:33
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

		if (!Schema::connection($db_connection)->hasTable('user_event_logs'))
		{
			Schema::connection($db_connection)->create('user_event_logs', function (Blueprint $table) {

				$table->char('user_event_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
				$table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

				$table->unsignedInteger('event_type')->notNull()->default(0)->comment('事件类型');
				$table->string('event_route_action',128)->notNull()->default('')->comment('事件路由');
				$table->string('event_name',64)->notNull()->default('')->comment('事件名称');
				$table->string('event_code',64)->notNull()->default('')->comment('事件编码');
				$table->text('note')->nullable()->comment('备注数据');

				$table->dateTime('created_at')->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				$table->unique('user_event_log_uid');
				$table->index('user_uid');
				$table->index('event_type');
				$table->index('created_time');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_event_logs` comment '用户事件日志表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_event_logs')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_event_logs');
		}
       
    }
};
