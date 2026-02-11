<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-10 15:23:50
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 11:47:41
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\database\migrations\Create\System\0001_01_01_000001_create_caches_table.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable1 = 'cache';
    protected $baseTable2 = 'cache_locks';
    protected $hasSnowflake = false;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = '';
    protected $tableComment1 = 'cache表';
    protected $tableComment2 = 'cache_locks表';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable1)) {
            Schema::connection($dbConnection)->create($this->baseTable1, function (Blueprint $table) {
                $table->string('key')->primary()->comment('主键key');
                $table->mediumText('value')->comment('cache锁值');
                $table->integer('expiration')->comment('有效期单位分钟');

                // 索引
                $table->index('expiration', 'idx_cache_expiration');
            });

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable1}` comment '{$this->tableComment1}'");
        }

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable2)) {
            Schema::connection($dbConnection)->create($this->baseTable2, function (Blueprint $table) {
                $table->string('key')->primary()->comment('主键key');
                $table->string('owner')->comment('所有者');
                $table->integer('expiration')->comment('有效期单位分钟');

                // 索引
                $table->index('owner', 'idx_cache_locks_owner');
                $table->index('expiration', 'idx_cache_locks_expiration');
            });

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable2}` comment '{$this->tableComment2}'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (Schema::connection($dbConnection)->hasTable($this->baseTable1)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable1);
        }

        if (Schema::connection($dbConnection)->hasTable($this->baseTable2)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable2);
        }
    }
};
