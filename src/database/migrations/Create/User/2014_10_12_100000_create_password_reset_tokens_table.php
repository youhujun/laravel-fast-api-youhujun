<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: YouHuJun
 * @Date: 2022-02-09 20:38:38
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 20:33:36
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
		
		if (!Schema::connection($db_connection)->hasTable('password_reset_tokens'))
		{
			Schema::connection($db_connection)->create('password_reset_tokens', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->string('email',128)->default('')->comment('邮箱');
				$table->char('phone',12)->default('')->comment('手机号');
				$table->string('token',255)->default('')->comment('令牌');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');

				$table->index('email', 'idx_pwd_tokens_email');
				$table->index('phone', 'idx_pwd_tokens_phone');
				$table->index('created_time', 'idx_pwd_tokens_cre_time');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}password_reset_tokens` comment '密码重置表'");
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

		if (Schema::connection($db_connection)->hasTable('password_reset_tokens')) 
		{
			Schema::connection($db_connection)->dropIfExists('password_reset_tokens');
		}
       
    }
};
