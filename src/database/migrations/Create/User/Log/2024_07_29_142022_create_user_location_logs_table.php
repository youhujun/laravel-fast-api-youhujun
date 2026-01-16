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

		if (!Schema::connection($db_connection)->hasTable('user_location_logs'))
		{
			Schema::connection($db_connection)->create('user_location_logs', function (Blueprint $table)
			{
				$table->char('user_location_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
				$table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
				$table->tinyInteger('type')->notNull()->default(0)->comment('类型 10用户');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->decimal('latitude',32,10,true)->notNull()->default(0)->comment('维度');
				$table->decimal('longitude',32,10,true)->notNull()->default(0)->comment('经度');
				$table->string('address',128)->notNull()->default('')->comment('位置信息');
				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				$table->unique('user_location_log_uid');
				$table->index('user_uid');
				$table->index('created_time');
				$table->index('sort');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_location_logs` comment '用户位置信息记录表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_location_logs')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_location_logs');
		}
        
    }
};
