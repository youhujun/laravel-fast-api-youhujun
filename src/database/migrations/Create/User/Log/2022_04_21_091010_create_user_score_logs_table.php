<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-21 09:10:10
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 14:45:33
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

        if (!Schema::connection($db_connection)->hasTable('user_score_logs')) {
            Schema::connection($db_connection)->create('user_score_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_score_log_uid')->comment('日志uid,雪花ID');
                // 分片键：user_uid%100，未来分库分表用
                $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键：user_uid%100，未来分库分表用');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->decimal('before_amount', 32, 8)->default(0)->comment('金额');
                $table->decimal('change_value', 32, 8)->default(0)->comment('变动数值');
                $table->unsignedTinyInteger('change_type')->default(0)->comment('0未知 10增加 20 减少');
                $table->decimal('amount', 32, 8)->default(0)->comment('系统币');
                $table->string('note', 32)->nullable()->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_score_log_uid', 'uni_user_score_logs_uid');
                $table->index('user_uid', 'idx_user_score_logs_user_uid');
                $table->index('created_time', 'idx_user_score_logs_created_time');
                $table->index('change_type', 'idx_user_score_logs_change_type');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_score_logs` comment '用户积分日志表'");
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

        if (Schema::connection($db_connection)->hasTable('user_score_logs')) {
            Schema::connection($db_connection)->dropIfExists('user_score_logs');
        }
    }
};
