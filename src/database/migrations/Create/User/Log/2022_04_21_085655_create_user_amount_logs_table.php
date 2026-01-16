<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-21 08:56:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:33:18
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

        if (!Schema::connection($db_connection)->hasTable('user_amount_logs')) {
            Schema::connection($db_connection)->create('user_amount_logs', function (Blueprint $table) {
                $table->char('user_amount_log_uid', 20)->notNull()->comment('日志uid,雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->decimal('before_amount', 32, 8)->notNull()->default(0)->comment('实际金额');
                $table->decimal('change_value', 32, 8)->notNull()->default(0)->comment('变动数值');
                $table->unsignedTinyInteger('change_type')->notNull()->default(0)->comment('变动类型 0未知 10增加 20减少');
                $table->decimal('amount', 32, 8)->notNull()->default(0)->comment('余额');
                $table->string('note', 32)->nullable()->comment('备注');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_amount_log_uid');
                $table->index('user_uid');
                $table->index('created_time');
                $table->index('change_type');
                $table->index('sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_amount_logs` comment '用户余额日志表'");
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

        if (Schema::connection($db_connection)->hasTable('user_amount_logs')) {
            Schema::connection($db_connection)->dropIfExists('user_amount_logs');
        }
    }
};
