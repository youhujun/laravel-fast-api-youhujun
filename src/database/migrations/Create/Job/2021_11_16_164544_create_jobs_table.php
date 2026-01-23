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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'jobs';
    protected $hasSnowflake = false;
    protected $tableComment = '队列表';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        //注意是否需要修改mysql连接名和表名
        if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->bigIncrements('id')->comment('主键');
                $table->string('queue')->default('')->comment('队列');
                $table->longText('payload')->default('')->comment('有效载荷');
                $table->unsignedTinyInteger('attempts')->default(0)->comment('允许尝试次数');
                $table->unsignedInteger('reserved_at')->nullable()->comment('重新尝试时间');
                $table->unsignedInteger('available_at')->comment('完成时间');
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');

                // 索引
                $table->index('queue', 'idx_jobs_queue');
            });

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable}` comment '{$this->tableComment}'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }
    }
};
