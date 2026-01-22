<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-12 10:52:49
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:36:56
 * @FilePath: \src\database\migrations\Create\User\Platform\2023_07_12_105249_create_user_wechat_unionid_table.php
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

        if (!Schema::connection($db_connection)->hasTable('user_wechat_unionids')) {
            Schema::connection($db_connection)->create('user_wechat_unionids', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('user_wechat_unionid_uid')->comment('用户微信unionid雪花ID');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                $table->string('unionid', 64)->nullable()->comment('微信的unionid 唯一');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_wechat_unionid_uid', 'uni_wechat_uids_uw_uid');
                $table->unique('unionid', 'uni_wechat_uids_unionid');
                $table->index('user_uid', 'idx_wechat_uids_user_uid');
                $table->index('created_time', 'idx_wechat_uids_cre_time');
                $table->index('sort', 'idx_wechat_uids_sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_wechat_unionids` comment '用户微信的unionid'");
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

        if (Schema::connection($db_connection)->hasTable('user_wechat_unionids')) {
            Schema::connection($db_connection)->dropIfExists('user_wechat_unionids');
        }
    }
};

