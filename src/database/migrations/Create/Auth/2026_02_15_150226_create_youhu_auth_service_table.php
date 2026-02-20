<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-15 15:02:26
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-15 19:25:06
 * @FilePath: \youhu-laravel-api-12\database\migrations\2026_02_15_150226_create_youhu_auth_service_table.php
 * Copyright (C) 2026 youhujun & xueer. All rights reserved.
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    /**
     * 基础表名（和ShardFacade::getTableName的baseTable对齐）
     */
    protected $baseTable = 'youhu_auth_service';
    //是否分片 仅做识别用,不参与代码逻辑
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = '';
    // 请填写表注释（如：游鹄生态-用户表）
    protected $tableComment = '游鹄生态鉴权表';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        // 读取ds_0
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        //分片
        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i; // 对齐ShardFacade::getTableName规则
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) { // 关键：把$i传入闭包
                    // 物理自增主键（仅数据库层面用，业务代码不碰）
                    $table->id()->comment('物理主键（自增）');
                    // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                    $table->unsignedBigInteger('user_uid')->comment('核心 AppId，与用户表 user_uid 一致	身份唯一锚点，微服务间认身份的核心');
                    // 分片键：user_id%100/ID%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->string('secret_key', 128)->default('')->comment('各微服务独立生成的秘钥（加密存储）');
                    $table->string('service_flag', 32)->default('youhu-base')->comment('微服务标识（youhu-base/youhu-main/youhu-shop）');
                    $table->cahr('auth_token', 36)->nullable()->comment('前端使用唯一标识');
                    // 状态字段
                    $table->unsignedTinyInteger('status')->default(1)->comment('账户状态 0禁用 1启用');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                    // 索引 - 关键修复：加$i区分分表索引名，避免重复
                    $table->unique('user_uid', 'uni_' . $this->baseTable . '_user_uid_' . $i);
                    $table->unique('auth_token', 'uni_' . $this->baseTable . '_auth_token_' . $i);
                    // 适配软删除
                    $table->index('created_time', 'idx_' . $this->baseTable . '_created_time_' . $i);
                });

                // 关键修复：补全单引号，修正SQL语法
                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
            }
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
        // 读取ds_0
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        //分片
        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
