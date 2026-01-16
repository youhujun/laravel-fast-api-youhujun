<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-24 10:53:32
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2023-08-30 15:08:27
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

		if (!Schema::connection($db_connection)->hasTable('user_level_item_unions'))
		{
			Schema::connection($db_connection)->create('user_level_item_unions', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedInteger('user_level_id')->notNull()->default(0)->comment('用户级别id');
				$table->unsignedInteger('level_item_id')->notNull()->default(0)->comment('级别配置项id');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->unsignedTinyInteger('value_type')->notNull()->default(0)->comment('与参数值的关系 10等于 20大于 30小于 40大于等于 50小于等于');
				$table->unsignedInteger('value')->notNull()->default(0)->comment('参数值');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				$table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');

			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_level_item_unions` comment '用户级别和配置项关联表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_level_item_unions'))
		{
			Schema::connection($db_connection)->dropIfExists('user_level_item_unions');
		}
        
    }
};
