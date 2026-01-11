<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-24 10:14:38
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-08-24 10:09:49
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

		if (!Schema::connection($db_connection)->hasTable('goods_class_union')) 
		{
			Schema::connection($db_connection)->create('goods_class_union', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('goods_id')->default(0)->comment('商品id');
				$table->unsignedBigInteger('goods_class_id')->default(0)->comment('分类id');
				$table->unsignedBigInteger('goods_class_one_depp_id')->default(0)->comment('一级分类id');
				$table->unsignedBigInteger('goods_class_two_depp_id')->default(0)->comment('二级分类id');
				$table->unsignedBigInteger('goods_class_three_depp_id')->default(0)->comment('三级分类id');
				$table->unsignedBigInteger('goods_class_four_depp_id')->default(0)->comment('四级分类id');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
	
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}goods_class_union` comment '商品与分类关联表'");
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
		
		if (Schema::connection($db_connection)->hasTable('goods_class_union')) 
		{
			Schema::connection($db_connection)->dropIfExists('goods_class_union');
		}
        
    }
};
