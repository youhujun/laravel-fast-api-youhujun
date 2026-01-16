<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-13 14:58:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:48:48
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
        if (!Schema::connection($db_connection)->hasTable('system_config')) {
            Schema::connection($db_connection)->create('user_role_unions', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedTinyInteger('role_id')->notNull()->default(0)->comment('角色id');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                // 索引
                $table->index('user_uid');
                $table->index('role_id');
                $table->index('created_time');
                $table->index('updated_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_role_unions` comment '用户和角色关联表'");
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

        if (Schema::connection($db_connection)->hasTable('user_role_unions')) {
            Schema::connection($db_connection)->dropIfExists('user_role_unions');
        }
    }
};
