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
		if (!Schema::connection($db_connection)->hasTable('user_amount')) 
		{
			Schema::connection($db_connection)->create('user_amount', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->unsignedBigInteger('user_id')->default(0)->comment('用户主键id');
				$table->string('userId')->default('')->comment('用户id');
				$table->decimal('amount',32,8)->default(0)->comment('余额');
				$table->decimal('bonus',32,8)->default(0)->comment('奖金');
				$table->decimal('prepare_bonus',32,8)->default(0)->comment('预计增加奖金');
				$table->decimal('coin',32,8)->default(0)->comment('系统币');
				$table->decimal('score',32,8)->default(0)->comment('积分');

				$table->string('note',128)->default('')->comment('备注');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				
			});

			//注意是否需要修改mysql连接名
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_amount` comment '用户余额表'");
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

		if (Schema::connection($db_connection)->hasTable('user_amount')) 
		{
			 Schema::connection($db_connection)->dropIfExists('user_amount');
		}
       
    }
};
