<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: YouHuJun
 * @Date: 2021-08-16 19:00:53
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-25 21:44:10
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

		if (!Schema::connection($db_connection)->hasTable('user_infos'))
		{
			Schema::connection($db_connection)->create('user_infos', function (Blueprint $table) {
				$table->id()->comment('主键');
				$table->unsignedBigInteger('user_info_uid')->comment('用户信息雪花ID');
				$table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->string('nick_name',64)->default('')->comment('昵称');
				$table->string('family_name',32)->default('')->comment('姓');
				$table->string('name',64)->default('')->comment('名');
				$table->string('real_name',128)->default('')->comment('真实姓名');
				$table->string('id_number',32)->nullable()->comment('身份证号');
				$table->unsignedTinyInteger('sex')->default(0)->comment('性别 0未知10男20女');
				$table->date('solar_birthday_at')->nullable()->comment('阳历生日');
				$table->unsignedInteger('solar_birthday_time')->default(0)->comment('阳历生日');
				$table->date('chinese_birthday_at')->nullable()->comment('阴日生日');
				$table->unsignedInteger('chinese_birthday_time')->default(0)->comment('阴日生日');
				$table->string('introduction',255)->default('')->comment('简介');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				// 索引
				$table->unique('user_info_uid', 'uni_user_infos_uid');
				$table->unique('id_number', 'uni_user_infos_id_number');
				$table->index('user_uid', 'idx_user_infos_user_uid');
				$table->index('created_time', 'idx_user_infos_created_time');
				$table->index('sex', 'idx_user_infos_sex');
				$table->index('solar_birthday_time', 'idx_user_infos_solar_birthday');
				$table->index('chinese_birthday_time', 'idx_user_infos_chinese_birthday');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_infos` comment '用户信息表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_infos')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_infos');
		}
        
    }
};
