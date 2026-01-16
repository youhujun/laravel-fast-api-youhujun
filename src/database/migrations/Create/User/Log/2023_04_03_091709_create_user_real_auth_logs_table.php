<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-04-03 09:17:09
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:35:41
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

        if (!Schema::connection($db_connection)->hasTable('user_real_auth_logs')) {
            Schema::connection($db_connection)->create('user_real_auth_logs', function (Blueprint $table) {
                $table->char('user_real_auth_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->char('admin_uid',20)->notNull()->default('')->comment('审核的管理员id');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('status')->notNull()->default(0)->comment('状态 10申请中  20通过 30拒绝');
                $table->dateTime('auth_apply_at')->nullable()->comment('实名认证申请时间string');
                $table->unsignedInteger('auth_apply_time')->notNull()->default(0)->comment('实名认证申请时间int');
                $table->dateTime('auth_at')->nullable()->comment('实名认证审核时间string');
                $table->unsignedInteger('auth_time')->notNull()->default(0)->comment('实名认证审核时间int');
                $table->string('refuse_info', 64)->notNull()->default('')->comment('拒绝原因');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_real_auth_log_uid');
                $table->index('user_uid');
                $table->index('auth_apply_time');
                $table->index('created_time');
                $table->index('auth_time');
                $table->index('sort');
                $table->index('status');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_real_auth_logs` comment '用户实名认证日志表'");
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

        if (Schema::connection($db_connection)->hasTable('user_real_auth_logs')) {
            Schema::connection($db_connection)->dropIfExists('user_real_auth_logs');
        }
    }
};
