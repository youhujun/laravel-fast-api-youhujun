<?php
/*
 * @Description: 
 * @version: v1
 * @Author: youhujun youhu8888@163.com & xueer 
 * @Date: 2026-05-17 20:33:10
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-17 20:43:59
 * @FilePath: \youhu-laravel-api-12\database\migrations\2026_05_17_203310_create_user_cascaders_table.php
 * Copyright (C) 2026 youhujun & xueer . All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{

	/**
     * 基础表名（和ShardFacade::getTableName的baseTable对齐）
     */
    protected $baseTable = 'user_cascaders';
	//是否分片 仅做识别用,不参与代码逻辑
    protected $hasSnowflake = false;
	// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = 'user_uid';
	// 请填写表注释（如：游鹄生态-用户表）
    protected $tableComment = '用户级联关系存储表-用于前端cascader回显'; 
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
                    $table->unsignedBigInteger('user_cascader_uid')->comment('全局唯一ID,雪花ID,业务核心ID');
                    $table->unsignedBigInteger('user_uid')->comment('用户雪花ID');
                    // 分片键：user_uid%(db_count * table_count)(工具包自动计算)
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%(db_count * table_count)(工具包自动计算)');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->string('role_cascader_json',255)->default('')->comment('角色关系级联');
                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

					
                    // 1. 主键唯一索引（自身雪花UID）
					$table->unique('user_cascader_uid', 'uni_primary_key' . $i);
					// 2. 业务计算分片索引（用于计算shard_key的UID）
					$table->index('user_uid', 'idx_bussiness_calc_' . $i);
					// 3. 最终分片键索引（真正的shard_key）
					$table->index('shard_key', 'idx_shard_key_' . $i);
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
