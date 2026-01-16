<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-01 15:27:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:48:39
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

		if (!Schema::connection($db_connection)->hasTable('personal_access_tokens'))
		{
			Schema::connection($db_connection)->create('personal_access_tokens', function (Blueprint $table) {
				$table->id()->comment('个人token表主键');

				$table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');

				$table->string('tokenable_type',255)->notNull()->comment('类型');
				$table->bigInteger('tokenable_id')->notNull()->comment('id');
				$table->string('name')->notNull()->comment('姓名');
				$table->string('token', 64)->notNull()->comment('token');
				$table->text('abilities')->nullable()->comment('能力');
				$table->dateTime('last_used_at')->nullable()->comment('最后使用时间');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');

				// 索引
				$table->index('user_uid');
				$table->index('tokenable_type');
				$table->index('tokenable_id');
				$table->index('last_used_at');
				$table->unique('token');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}personal_access_tokens` comment '个人token表'");
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
		
		if (Schema::connection($db_connection)->hasTable('personal_access_tokens')) 
		{
			Schema::connection($db_connection)->dropIfExists('personal_access_tokens');
		}
       
    }
};
