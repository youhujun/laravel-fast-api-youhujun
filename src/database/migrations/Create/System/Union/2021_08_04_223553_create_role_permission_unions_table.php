<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-13 14:58:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2025-11-14 18:14:01
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

		if (!Schema::connection($db_connection)->hasTable('role_permission_unions'))
		{
			Schema::connection($db_connection)->create('role_permission_unions', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('permission_id')->notNull()->default(0)->comment('用户id');
				$table->unsignedBigInteger('role_id')->notNull()->default(0)->comment('角色id');

				// 索引
				$table->index('permission_id');
				$table->index('role_id');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}role_permission_unions` comment '角色和权限菜单关联表'");
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
		
		if (Schema::connection($db_connection)->hasTable('role_permission_unions'))
		{
			Schema::connection($db_connection)->dropIfExists('role_permission_unions');
		}
        
    }
};
