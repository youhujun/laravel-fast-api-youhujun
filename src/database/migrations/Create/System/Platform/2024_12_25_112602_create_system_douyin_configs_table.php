<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-18 22:54:46
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:59:20
 * @FilePath: \src\database\migrations\Create\System\Platform\2024_12_25_112602_create_system_douyin_configs_table.php
 * Copyright (C) 2026 youhujun. All rights reserved.
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

        if (!Schema::connection($db_connection)->hasTable('system_douyin_configs')) {
            Schema::connection($db_connection)->create('system_douyin_configs', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->string('name', 64)->notNull()->default('')->comment('名称');
                $table->unsignedtinyInteger('type')->notNull()->default(0)->comment('类型 10小程序 20小游戏');
                $table->string('appid', 64)->nullable()->comment('appid');
                $table->string('appsecret', 64)->notNull()->default('')->comment('appsecret');
                $table->string('note', 128)->notNull()->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->index('type');
                $table->index('created_time');
                $table->unique(['appid','deleted_at']);
                $table->unique(['name','deleted_at']);
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}system_douyin_configs` comment '系统抖音配置表'");
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

        if (Schema::connection($db_connection)->hasTable('system_douyin_configs')) {
            Schema::connection($db_connection)->dropIfExists('system_douyin_configs');
        }
    }
};
