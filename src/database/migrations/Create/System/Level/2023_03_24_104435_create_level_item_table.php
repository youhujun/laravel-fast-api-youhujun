<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-24 10:44:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2023-08-22 11:13:53
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

		if (!Schema::connection($db_connection)->hasTable('level_items'))
		{
			Schema::connection($db_connection)->create('level_items', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

				$table->unsignedTinyInteger('type')->notNull()->default(0)->comment('配置项类型 10数值 20百分比 30时间');
				$table->string('item_name',32)->unique()->nullable()->comment('配置项名称 唯一');
				$table->string('item_code',32)->unique()->nullable()->comment('配置项代码 唯一');
				$table->string('description',64)->notNull()->default('')->comment('描述');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

				// 索引
				$table->index('type');
				$table->index('created_time');

			});

			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::statement("ALTER TABLE `{$prefix}level_items` comment '级别配置项表'");
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
		
		if (Schema::connection($db_connection)->hasTable('level_items'))
		{
			Schema::dropIfExists('level_items');
		}
       
    }
};
