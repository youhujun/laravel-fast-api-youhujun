<?php
/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun youhu8888@163.com
 * @Date: 2025-11-28 12:04:27
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:05:57
 * @FilePath: d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api\src\database\migrations\Create\System\Config\2025_11_28_120427_create_system_withdraw_config.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'system_withdraw_configs';
    protected $hasSnowflake = false;
    protected $tableComment = '系统提现配置表';

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
        if (!Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table)
            {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->char('admin_uid', 20)->default('')->comment('管理员uid,雪花ID');
                $table->string('item_name',32)->unique()->nullable()->comment('字段名称 唯一');
                $table->string('item_value',32)->default('')->comment('字段值');
                $table->unsignedTinyInteger('value_type')->default(10)->comment('值得类型 10整数 20小数');
                $table->string('note',128)->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->index('value_type', 'idx_sys_withdraw_cfgs_val_type');
            });

            //注意是否需要修改mysql连接名
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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }


    }
};
