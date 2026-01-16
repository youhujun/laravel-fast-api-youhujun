<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-23 16:53:30
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-04-06 16:24:31
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

		if (!Schema::connection($db_connection)->hasTable('user_phones'))
		{
			Schema::connection($db_connection)->create('user_phones', function (Blueprint $table) {
				$table->id()->comment('主键');
				$table->char('user_phone_uid', 20)->notNull()->comment('用户电话雪花ID');
				$table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->unsignedTinyInteger('type')->notNull()->default(100)->comment('类型 10紧急联系人');
				$table->unsignedTinyInteger('is_default')->notNull()->default(0)->comment('是否默认 0不 1是');
				$table->string('phone',12)->notNull()->default('')->comment('电话');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				// 索引
				$table->unique('user_phone_uid');
				$table->index('user_uid');
				$table->index('created_time');
				$table->index('type');
				$table->index('is_default');
				$table->index('sort');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
			
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_phones` comment '用户联系电话表'");
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
		
		if (Schema::connection($db_connection)->hasTable('user_phones')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_phones');
		}
        
    }
};
