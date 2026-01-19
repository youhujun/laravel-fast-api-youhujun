<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 17:06:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-07 10:52:16
 * @FilePath: d:\wwwroot\Api\Components\Laravel\youhujun\laravel-fast-api\src\database\migrations\User\Log\2023_08_10_105750_create_user_upload_file_log_table.php
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

		if (!Schema::connection($db_connection)->hasTable('user_upload_file_logs'))
		{
			Schema::connection($db_connection)->create('user_upload_file_logs', function (Blueprint $table)
			{
			   $table->char('user_upload_file_log_uid', 20)->comment('日志uid,雪花ID');
			   $table->char('user_uid', 20)->default('')->comment('用户uid');
			   $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

			   $table->unsignedTinyInteger('use_type')->default(0)->comment('使用类型0  10个人配置 20个人文件');
			   $table->unsignedTinyInteger('save_type')->default(0)->comment('存储类型 10本地 20存储桶');

			   $table->string('file_name',128)->default('')->comment('文件名');
			   $table->string('file_path',128)->default('')->comment('文件路径');
			   $table->string('file_extension',12)->default('')->comment('文件后缀');
			   $table->string('file',128)->default('')->comment('文件');
			   $table->string('file_url',255)->default('')->comment('文件url(存储桶类型)');

			   $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
			   $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
			   $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
			   $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
			   $table->dateTime('deleted_at')->nullable()->comment('删除时间');

			   $table->unique('user_upload_file_log_uid', 'uni_user_upload_file_logs_uid');
			   $table->index('user_uid', 'idx_user_upload_file_logs_user_uid');
			   $table->index('created_time', 'idx_user_upload_file_logs_created');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_upload_file_logs` comment '用户文件上传日志表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_upload_file_logs')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_upload_file_logs');
		}
       
    }
};
