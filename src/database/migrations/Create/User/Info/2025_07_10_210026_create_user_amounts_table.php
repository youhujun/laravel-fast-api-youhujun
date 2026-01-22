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
		if (!Schema::connection($db_connection)->hasTable('user_amounts'))
		{
			Schema::connection($db_connection)->create('user_amounts', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('user_amount_uid')->comment('用户余额雪花ID');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
				$table->decimal('amount',32,8)->default(0)->comment('余额');
				$table->decimal('bonus',32,8)->default(0)->comment('奖金');
				$table->decimal('prepare_bonus',32,8)->default(0)->comment('预计增加奖金');
				$table->decimal('coin',32,8)->default(0)->comment('系统币');
				$table->decimal('score',32,8)->default(0)->comment('积分');

				$table->string('note',128)->default('')->comment('备注');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				// 索引
				$table->unique('user_amount_uid', 'uni_user_amounts_uid');
				$table->index('user_uid', 'idx_user_amounts_user_uid');
				$table->index('created_time', 'idx_user_amounts_created_time');
				$table->index('sort', 'idx_user_amounts_sort');
			});

			//注意是否需要修改mysql连接名
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_amounts` comment '用户余额表'");
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

		if (Schema::connection($db_connection)->hasTable('user_amounts')) 
		{
			 Schema::connection($db_connection)->dropIfExists('user_amounts');
		}
       
    }
};
