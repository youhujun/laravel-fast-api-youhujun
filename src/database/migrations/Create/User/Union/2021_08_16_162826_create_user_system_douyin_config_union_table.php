<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 16:28:26
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-25 14:01:01
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

		if (!Schema::connection($db_connection)->hasTable('system_config')) 
		{
			Schema::connection($db_connection)->create('user_system_douyin_config_union', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('user_id')->default(0)->index()->comment('用户id');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->string('openid',100)->unique()->nullable()->comment('唯一openid');
				$table->string('session_key',100)->default('')->comment('session_key');
				$table->unsignedInteger('system_douyin_config_id')->default(0)->comment('系统抖音配置id');
				$table->dateTime('verified_at')->nullable()->comment('微信号认证时间string');
				$table->unsignedInteger('verified_time')->default(0)->comment('微信号认证时间int');
				
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
	
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_system_douyin_config_union` comment '用户和系统抖音配置关联表openid'");
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

		if (Schema::connection($db_connection)->hasTable('user_system_douyin_config_union')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_system_douyin_config_union');
		}
       
    }
};
