<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-10-18 22:54:46
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 * @FilePath: \src\database\migrations\Create\System\Platform\2024_12_25_112602_create_system_douyin_configs_table.php
 * Copyright (C) 2026 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'system_douyin_configs';
    protected $hasSnowflake = false;
		// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = '';
    protected $tableComment = '系统抖音配置表';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->string('name', 64)->default('')->comment('名称');
                $table->unsignedTinyInteger('type')->default(0)->comment('类型 10小程序 20小游戏');
                $table->string('appid', 64)->nullable()->comment('appid');
                $table->string('appsecret', 64)->default('')->comment('appsecret');
                $table->string('note', 128)->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->index('type', 'idx_sys_douyin_cfgs_type');
                $table->index('created_time', 'idx_sys_douyin_cfgs_cre_time');
                $table->unique(['appid','deleted_at'], 'uni_sys_douyin_cfgs_appid_del');
                $table->unique(['name','deleted_at'], 'uni_sys_douyin_cfgs_name_del');
            });

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable}` comment '{$this->tableComment}'");
        }
    }

    /**
     * Reverse the migrations.
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
