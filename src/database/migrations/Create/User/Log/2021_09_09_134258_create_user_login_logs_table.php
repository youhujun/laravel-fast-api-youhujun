<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-09 13:42:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:31:35
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

        if (!Schema::connection($db_connection)->hasTable('user_login_logs')) {
            Schema::connection($db_connection)->create('user_login_logs', function (Blueprint $table) {
                $table->char('user_login_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('status')->notNull()->default(0)->comment('状态 0未知 10登录 20退出');
                $table->string('instruction', 128)->notNull()->default('')->comment('说明');
                $table->string('ip', 32)->notNull()->default('')->comment('ip地址');
                $table->unsignedTinyInteger('source_type')->notNull()->default(0)->comment('登录来源类型 10phone 20pc');

                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_login_log_uid');
                $table->index('user_uid');
                $table->index('created_time');
                $table->index('status');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_login_logs` comment '用户登录退出日志表'");
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

        if (Schema::connection($db_connection)->hasTable('user_login_logs')) {
            Schema::connection($db_connection)->dropIfExists('user_login_logs');
        }
    }
};
