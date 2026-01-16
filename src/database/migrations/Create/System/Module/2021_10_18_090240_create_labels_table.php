<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-10-18 09:02:40
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2023-09-11 20:34:38
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

		if (!Schema::connection($db_connection)->hasTable('labels'))
		{
			Schema::connection($db_connection)->create('labels', function (Blueprint $table) {
				$table->id()->comment('主键');

				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->unsignedInteger('parent_id')->notNull()->default(0)->comment('父级id');
				$table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('级别');
				$table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否|开关 0关否1是开');
				$table->string('label_name',64)->notNull()->default('')->comment('标签名称');
				$table->string('label_code',64)->nullable()->comment('标签代码');
				$table->unsignedBigInteger('label_picture_id')->notNull()->default(0)->comment('标签图片id(相册图片id)');
				$table->string('note',255)->nullable()->comment('备注说明');
				$table->unsignedTinyInteger('sort')->notNull()->default(0)->comment('排序');

				// 索引
				$table->index('label_code');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

			 });

			 $prefix = config('database.connections.'.$db_connection.'.prefix');

			 DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}labels` comment '标签表'");
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
		
		if (Schema::connection($db_connection)->hasTable('labels'))
		{
			Schema::connection($db_connection)->dropIfExists('labels');
		}
        
    }
};
