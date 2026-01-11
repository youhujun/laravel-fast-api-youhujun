<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-25 11:44:12
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-20 00:12:14
 * @FilePath: d:\wwwroot\Working\YouHu\Components\Laravel\youhujun\laravel-fast-api\src\database\migrations\System\Platform\2024_12_25_112602_create_system_wechat_config_table.php
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

		if (!Schema::connection($db_connection)->hasTable('system_wechat_config')) 
		{
			Schema::connection($db_connection)->create('system_wechat_config', function (Blueprint $table)
			{
				$table->id()->comment('主键');
				$table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
				$table->string('name',64)->default('')->comment('名称');
				$table->unsignedtinyInteger('type')->default(0)->comment('类型 10小程序 20小游戏 30公众号 40 测试微信公众号');
				$table->string('appid',64)->unique()->nullable()->comment('appid');
				$table->string('appsecret',64)->default('')->comment('appsecret');
				
				$table->string('note',128)->default('')->comment('备注');
				$table->dateTime('created_at')->useCurrent()->comment('创建时间string');
				$table->unsignedInteger('created_time')->index(0)->default(0)->comment('创建时间int');
				$table->dateTime('updated_at')->nullable()->comment('更新时间string');
				$table->unsignedInteger('updated_time')->default(0)->comment('更新时间int');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间string');
				$table->unsignedInteger('deleted_time')->default(0)->comment('删除时间int');
				$table->unsignedTinyInteger('sort')->default(100)->comment('排序');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}system_wechat_config` comment '系统微信配置表'");
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
		
		if (Schema::connection($db_connection)->hasTable('system_wechat_config')) 
		{	
			Schema::connection($db_connection)->dropIfExists('system_wechat_config');
		}
       
    }
};
