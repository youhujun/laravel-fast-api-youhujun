<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-25 09:57:23
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-12-25 10:01:40
 * @FilePath: \database\migrations\2024_12_25_095723_create_user_douyin_unionid_table.php
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

        if (!Schema::connection($db_connection)->hasTable('user_douyin_unionids')) {
            Schema::connection($db_connection)->create('user_douyin_unionids', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_douyin_unionid_uid', 20)->comment('用户抖音unionid雪花ID');
                $table->char('user_uid', 20)->default('')->comment('用户uid');
                $table->string('unionid', 64)->nullable()->comment('抖音unionid 唯一');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_douyin_unionid_uid', 'uni_douyin_uids_ud_uid');
                $table->unique('unionid', 'uni_douyin_uids_unionid');
                $table->index('user_uid', 'idx_douyin_uids_user_uid');
                $table->index('created_time', 'idx_douyin_uids_cre_time');
                $table->index('sort', 'idx_douyin_uids_sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_douyin_unionids` comment '用户抖音的unionid'");
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

        if (Schema::connection($db_connection)->hasTable('user_douyin_unionids')) {
            Schema::connection($db_connection)->dropIfExists('user_douyin_unionids');
        }
    }
};
