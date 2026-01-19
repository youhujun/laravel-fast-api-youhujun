<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-23 15:35:15
 * @LastEditors: YouHuJun
 * @LastEditTime: 2022-08-22 14:44:57
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
		if (!Schema::connection($db_connection)->hasTable('failed_jobs'))
		{
			Schema::connection($db_connection)->create('failed_jobs', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

				$table->dateTime('failed_at')->useCurrent()->comment('失败时间');
				$table->unsignedInteger('failed_time')->default(0)->comment('失败时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				$table->string('uuid',100)->comment('唯一标识');
				$table->text('connection')->comment('连接');
				$table->text('queue')->comment('队列');
				$table->longtext('payload')->comment('有效载荷');
				$table->longtext('exception')->nullable()->comment('异常');

				// 索引
				$table->unique('uuid', 'uni_failed_jobs_uuid');
				$table->index('failed_time', 'idx_failed_jobs_failed_time');

			});
			
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}failed_jobs` comment '失败队列表'");
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
		if (Schema::connection($db_connection)->hasTable('failed_jobs')) 
		{
			Schema::connection($db_connection)->dropIfExists('failed_jobs');
		}	
       
    }
};
