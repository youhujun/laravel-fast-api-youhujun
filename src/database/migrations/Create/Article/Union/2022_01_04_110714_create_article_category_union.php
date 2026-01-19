<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-01-04 11:07:14
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2023-08-22 09:25:48
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
		if (!Schema::connection($db_connection)->hasTable('article_category_unions'))
		{
			Schema::connection($db_connection)->create('article_category_unions', function (Blueprint $table)
			{

			   $table->id()->comment('主键');
			   $table->char('article_category_union_uid', 20)->comment('文章分类关联雪花ID');
			   $table->char('article_uid', 20)->default('')->comment('文章uid,雪花ID');
			   $table->unsignedInteger('category_id')->default(0)->comment('文章分类id');
			   $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

			   // 时间字段（自动填充+索引，关键优化）
			   $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
			   $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
			   $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
			   $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
			   $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

			   // 索引
			   $table->unique('article_category_union_uid', 'uni_article_cat_uns_union_uid');
			   $table->index('article_uid', 'idx_article_cat_uns_article_uid');

			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}article_category_unions` comment '文章和分类关联表'");
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
		if (Schema::connection($db_connection)->hasTable('article_category_unions'))
		{
			Schema::connection($db_connection)->dropIfExists('article_category_unions');
		}
        
    }
};
