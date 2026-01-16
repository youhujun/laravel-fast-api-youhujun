<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-01-04 10:13:44
 * @LastEditors: YouHuJun
 * @LastEditTime: 2022-01-04 11:06:28
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
		//注意是否需要修改mysql连接名和表名
		if (!Schema::connection($db_connection)->hasTable('article_infos'))
		{
			Schema::connection($db_connection)->create('article_infos', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->char('article_uid', 20)->notNull()->default('')->comment('文章uid,雪花ID');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->text('article_info')->nullable()->comment('文章详情');

				// 时间字段（自动填充+索引，关键优化）
				$table->dateTime('created_at')->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

				// 索引
				$table->index('article_uid');

			 });
		}
       
		$prefix = config('database.connections.'.$db_connection.'.prefix');

        DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}article_infos` comment '文章详情表'");


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
		if (Schema::connection($db_connection)->hasTable('article_infos'))
		{
			Schema::connection($db_connection)->dropIfExists('article_infos');
		}
        
    }
};
