<?php

/*
 * @Description:
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer
 * @Date: 2026-02-22 10:51:02
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-02-22 16:33:56
 * @FilePath: \youhu-laravel-api-12\database\migrations\2026_02_22_105102_create_api_event_logs_table.php
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
    protected $baseTable = 'api_event_logs';
    //是否分片 仅做识别用,不参与代码逻辑
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    // 请填写表注释（如：游鹄生态-用户表）
    protected $tableComment = '游鹄生态-API事件日志表';
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

                    $table->unsignedBigInteger('api_event_log_uid')->comment('日志uid,雪花ID');

                    $table->unsignedBigInteger('user_uid')->comment('用户ID,雪花ID,业务核心ID');
                    // 分片键：user_id%100/ID%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    // 状态字段
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                    $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');

                    $table->string('service_code', 64)->default('')->comment('所属服务编码（youhu-base/youhu/youhushop）');
                    $table->string('request_id', 64)->default('')->comment('请求链路ID（全链路追踪）');
                    $table->unsignedTinyInteger('operator_type')->default(0)->comment('操作人类型：1-用户 2-系统 3-管理员 4-第三方');
                    $table->unsignedBigInteger('operator_uid')->default(0)->comment('操作人UID（用户/管理员UID，系统则为0）');
                    $table->unsignedTinyInteger('status')->default(0)->comment('事件状态：0-待处理 1-成功 2-失败 3-重试中');
                    $table->text('ext_json')->nullable()->comment('扩展字段（JSON格式，存储个性化数据）');


                    $table->unsignedInteger('event_type')->default(0)->comment('事件类型');
                    $table->string('event_route_action', 128)->default('')->comment('事件路由');
                    $table->string('event_name', 64)->default('')->comment('事件名称');
                    $table->string('event_code', 64)->default('')->comment('事件编码');
                    $table->text('note')->nullable()->comment('备注数据');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                    $table->unique('api_event_log_uid', 'uni_api_event_logs_uid_' . $i);
                    $table->index('user_uid', 'idx_api_event_logs_user_uid_' . $i);
                    $table->index('event_type', 'idx_api_event_logs_event_type_' . $i);
                    $table->index('created_time', 'idx_api_event_logs_created_time_' . $i);
                    $table->index('data_type', 'idx_api_event_logs_data_type_' . $i);

                    $table->index('service_code', 'idx_api_event_logs_service_code_' . $i);
                    $table->index('request_id', 'idx_api_event_logs_request_id_' . $i);
                    $table->index('status', 'idx_api_event_logs_status_' . $i);
                    $table->index(['operator_type', 'operator_uid'], 'idx_api_event_logs_operator_' . $i);
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
