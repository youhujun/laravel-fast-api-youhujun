<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-25 11:04:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:41:04
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

        if (!Schema::connection($db_connection)->hasTable('user_source_unions')) {
            Schema::connection($db_connection)->create('user_source_unions', function (Blueprint $table) {
                $table->id()->comment('主键--用户父关联表');
                $table->char('user_source_union_uid', 20)->comment('用户来源关联雪花ID');
                $table->char('user_uid', 20)->default('')->comment('用户uid');
                $table->unsignedBigInteger('first_id')->default(0)->comment('一级id');
                $table->unsignedBigInteger('second_id')->default(0)->comment('二级id');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                // 索引
                $table->unique('user_source_union_uid', 'uni_source_unions_us_uid');
                $table->index('user_uid', 'idx_source_unions_user_uid');
                $table->index('first_id', 'idx_source_unions_first_id');
                $table->index('second_id', 'idx_source_unions_sec_id');
                $table->index('created_time', 'idx_source_unions_cre_time');
                $table->index('sort', 'idx_source_unions_sort');
                $table->index('updated_time', 'idx_source_unions_upd_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_source_unions` comment '用户父关联表'");
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

        if (Schema::connection($db_connection)->hasTable('user_source_unions')) {
            Schema::connection($db_connection)->dropIfExists('user_source_unions');
        }
    }
};
