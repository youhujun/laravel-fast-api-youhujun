<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-10 15:23:50
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 20:11:37
 * @FilePath: \database\migrations\bak\0001_01_01_000001_create_cache_table.php
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
    protected $tableComment1 = 'cache表';
    protected $tableComment2 = 'cache_locks表';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable1))
        {
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

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable2))
        {
            Schema::connection($dbConnection)->create($this->baseTable2, function (Blueprint $table)
            {
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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable1))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable1);
        }

        if (Schema::connection($dbConnection)->hasTable($this->baseTable2))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable2);
        }
    }
};
