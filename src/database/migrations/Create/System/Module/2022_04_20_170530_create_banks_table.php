<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-20 17:05:30
 * @LastEditors: YouHuJun
 * @LastEditTime: 2022-05-30 11:11:44
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

		if (!Schema::connection($db_connection)->hasTable('banks'))
		{
			Schema::connection($db_connection)->create('banks', function (Blueprint $table) {

				$table->id()->comment('银行表主键');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->string('bank_name',32)->nullable()->comment('银行名称 唯一');
				$table->string('bank_code',32)->nullable()->comment('银行名称代码 唯一');
				$table->unsignedTinyInteger('is_default')->notNull()->default(0)->comment('是否常用 0不 1是');
				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

				// 索引
				$table->index('bank_name');
				$table->index('bank_code');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');
			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}banks` comment '系统银行表'");
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

		if (Schema::connection($db_connection)->hasTable('banks'))
		{
			Schema::connection($db_connection)->dropIfExists('banks');
		}
        
    }
};
