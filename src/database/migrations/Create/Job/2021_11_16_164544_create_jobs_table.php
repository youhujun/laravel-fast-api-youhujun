<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-18 22:54:46
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:28:29
 * @FilePath: \src\database\migrations\Create\Job\2021_11_16_164544_create_jobs_table.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');
        //注意是否需要修改mysql连接名和表名
        if (!Schema::connection($db_connection)->hasTable('jobs')) {
            Schema::connection($db_connection)->create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id')->comment('主键');
                $table->string('queue')->default('')->comment('队列');
                $table->longText('payload')->default('')->comment('有效载荷');
                $table->unsignedTinyInteger('attempts')->default(0)->comment('允许尝试次数');
                $table->unsignedInteger('reserved_at')->nullable()->comment('重新尝试时间');
                $table->unsignedInteger('available_at')->comment('完成时间');
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');

                // 索引
                $table->index('queue');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}jobs` comment '队列表'");
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
        //注意是否需要修改mysql连接名和表名
        if (Schema::connection($db_connection)->hasTable('jobs')) {
            Schema::connection($db_connection)->dropIfExists('jobs');
        }
    }
};
