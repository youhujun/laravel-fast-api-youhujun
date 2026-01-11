<?php

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
		if (!Schema::connection($db_connection)->hasTable('permission_param')) 
		{
			Schema::connection($db_connection)->create('permission_param', function (Blueprint $table)
			{
				$table->id()->comment('主键-权限菜单参数表(资源路由用)');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->unsignedBigInteger('permission_id')->default(0)->comment('权限菜单表id');
				$table->string('key',128)->default()->comment('字段名');
				$table->string('value',255)->default('')->comment('字段值');
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

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}permission_param` comment '权限菜单参数表(资源路由用)'");
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

		if (Schema::connection($db_connection)->hasTable('permission_param')) 
		{
			Schema::connection($db_connection)->dropIfExists('permission_param');
		}
       
    }
};
