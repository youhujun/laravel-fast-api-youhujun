<?php

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
		//注意是否需要修改mysql连接名和表名
		if (!Schema::connection($db_connection)->hasTable('jobs')) 
		{
			Schema::connection($db_connection)->create('jobs', function (Blueprint $table) {
				$table->bigIncrements('id')->comment('主键');;
				$table->string('queue')->index()->comment('队列');
				$table->longText('payload')->comment('有效载荷');
				$table->unsignedTinyInteger('attempts')->comment('允许尝试次数');
				$table->unsignedInteger('reserved_at')->nullable()->comment('重新尝试时间');
				$table->unsignedInteger('available_at')->comment('完成时间');
				$table->unsignedInteger('created_at')->comment('创建时间');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}jobs` comment '队列表'");
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
		if (Schema::connection($db_connection)->hasTable('jobs')) 
		{	
			Schema::connection($db_connection)->dropIfExists('jobs');
		}
        
    }
};
