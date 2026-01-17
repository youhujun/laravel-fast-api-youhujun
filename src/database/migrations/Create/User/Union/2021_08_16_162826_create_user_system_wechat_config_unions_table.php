<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 16:28:26
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-02-20 00:29:22
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

		if (!Schema::connection($db_connection)->hasTable('user_system_wechat_config_unions'))
		{
			Schema::create('user_system_wechat_config_unions', function (Blueprint $table) {

				$table->id()->comment('主键');
				$table->char('user_system_wechat_config_union_uid', 20)->notNull()->comment('用户微信配置关联雪花ID');
				$table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
				$table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
				$table->string('openid',100)->nullable()->comment('唯一openid');
				$table->string('session_key',100)->notNull()->default('')->comment('session_key');
				$table->unsignedTinyInteger('type')->notNull()->default(30)->comment('类型 10小程序 20小游戏 30公众号 40 测试微信公众号');
				$table->unsignedInteger('system_wechat_config_id')->notNull()->default(0)->comment('系统微信配置id');

				$table->string('access_token',128)->notNull()->default('')->comment('网页授权接口调用凭证,注意,此access_token与基础支持的access_token不同');
				$table->unsignedInteger('expires_in')->notNull()->comment('access_token接口调用凭证超时时间,单位(秒)');

				$table->string('refresh_token',128)->notNull()->default('')->comment('用户刷新access_token,有效期30天当refresh_token失效之后，需要用户重新授权。');

				$table->string('scope',30)->notNull()->default('')->comment('用户授权的作用域，使用逗号（,）分隔');

				$table->unsignedTinyInteger('is_snapshotuser')->notNull()->default(0)->comment('是否为快照页模式虚拟账号，只有当用户是快照页模式虚拟账号时返回，值为1');

				$table->dateTime('verified_at')->nullable()->comment('微信号认证时间string');
				$table->unsignedInteger('verified_time')->notNull()->default(0)->comment('微信号认证时间int');

				$table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
				$table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
				$table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
				$table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
				$table->dateTime('deleted_at')->nullable()->comment('删除时间');

				// 索引
				$table->unique('user_system_wechat_config_union_uid');
				$table->unique('openid');
				$table->index('user_uid');
				$table->index('created_time');
				$table->index('type');
				$table->index('system_wechat_config_id');
				$table->index('access_token');
				$table->index('refresh_token');
				$table->index('updated_time');
			});
	
			$prefix = config('database.connections.'.$db_connection.'.prefix');
	
			DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_system_wechat_config_unions` comment '用户和系统微信配置关联表openid'");
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

		if (Schema::connection($db_connection)->hasTable('user_system_wechat_config_unions')) 
		{
			Schema::connection($db_connection)->dropIfExists('user_system_wechat_config_unions');
		}
        
    }
};
