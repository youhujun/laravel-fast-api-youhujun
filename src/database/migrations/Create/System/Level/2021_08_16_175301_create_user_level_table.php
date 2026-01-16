<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 17:53:01
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-25 23:40:31
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

		if (!Schema::connection($db_connection)->hasTable('user_levels'))
		{
			Schema::connection($db_connection)->create('user_levels', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

				$table->string('level_name',32)->unique()->nullable()->comment('级别名称');
				$table->string('level_code',32)->unique()->nullable()->comment('级别代码');
				$table->decimal('amount',32,8,true)->notNull()->default(0)->comment('金额');
				$table->unsignedBigInteger('background_id')->notNull()->default(0)->comment('背景图标');
				$table->string('note',128)->notNull()->default('')->comment('备注信息');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

				// 索引
				$table->index('background_id');
				$table->index('created_time');

			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_levels` comment '用户级别表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_levels'))
		{
			Schema::connection($db_connection)->dropIfExists('user_levels');
		}
        
    }
};
