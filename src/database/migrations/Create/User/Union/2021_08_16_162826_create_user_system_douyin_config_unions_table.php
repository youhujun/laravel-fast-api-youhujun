<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 16:28:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:48:18
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');

        if (!Schema::connection($db_connection)->hasTable('user_system_douyin_config_unions')) {
            Schema::connection($db_connection)->create('user_system_douyin_config_unions', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_system_douyin_config_union_uid', 20)->notNull()->comment('用户抖音配置关联雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->string('openid', 100)->nullable()->comment('唯一openid');
                $table->string('session_key', 100)->notNull()->default('')->comment('session_key');
                $table->unsignedInteger('system_douyin_config_id')->notNull()->default(0)->comment('系统抖音配置id');
                $table->dateTime('verified_at')->nullable()->comment('微信号认证时间string');
                $table->unsignedInteger('verified_time')->notNull()->default(0)->comment('微信号认证时间int');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                // 索引
                $table->unique('user_system_douyin_config_union_uid');
                $table->unique('openid');
                $table->index('user_uid');
                $table->index('created_time');
                $table->index('verified_time');
                $table->index('system_douyin_config_id');
                $table->index('updated_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_system_douyin_config_unions` comment '用户和系统抖音配置关联表openid'");
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

        if (Schema::connection($db_connection)->hasTable('user_system_douyin_config_unions')) {
            Schema::connection($db_connection)->dropIfExists('user_system_douyin_config_unions');
        }
    }
};
