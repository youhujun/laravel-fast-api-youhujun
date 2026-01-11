<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-25 09:57:23
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-25 10:01:40
 * @FilePath: \database\migrations\2024_12_25_095723_create_user_douyin_unionid_table.php
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

		if (!Schema::connection($db_connection)->hasTable('user_douyin_unionid')) 
		{
			Schema::create('user_douyin_unionid', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('user_id')->default(0)->comment('用户id');
				$table->string('unionid',64)->unique()->nullable()->comment('抖音unionid 唯一');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_douyin_unionid` comment '用户抖音的unionid'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_douyin_unionid')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_douyin_unionid');
		}
        
    }
};
