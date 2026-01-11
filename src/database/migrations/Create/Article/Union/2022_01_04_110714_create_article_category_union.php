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
		if (!Schema::connection($db_connection)->hasTable('article_category_union')) 
		{
			Schema::connection($db_connection)->create('article_category_union', function (Blueprint $table) 
			{
	
			   $table->id()->comment('主键');
			   $table->unsignedBigInteger('article_id')->default(0)->comment('文章id');
			   $table->unsignedInteger('category_id')->default(0)->comment('文章分类id');
			   $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
	
			   $table->dateTime('created_at')->useCurrent()->comment('创建时间string');
			   $table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
			   $table->dateTime('deleted_at')->nullable()->comment('删除时间string');
			   $table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
	
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}article_category_union` comment '文章和分类关联表'");
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
		if (Schema::connection($db_connection)->hasTable('article_category_union')) 
		{
			Schema::connection($db_connection)->dropIfExists('article_category_union');
		}
        
    }
};
