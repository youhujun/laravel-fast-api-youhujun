<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-13 14:58:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:05:57
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_role_unions';
    protected $hasSnowflake = true;
    protected $tableComment = '用户和角色关联表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        if ($this->hasSnowflake) {
            for ($i = 0; $i < $tableCount; $i++) {
                $tableName = $this->baseTable . '_' . $i;
                if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                    Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                        $table->id()->comment('主键');
                        $table->unsignedBigInteger('user_role_union_uid')->comment('用户角色关联雪花ID');
                        $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                        $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                        $table->unsignedInteger('role_id')->default(0)->comment('角色id');

                        $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                        $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                        $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                        $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                        $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                        // 索引
                        $table->unique('user_role_union_uid', 'uni_role_unions_ur_uid_' . $i);
                        $table->index('user_uid', 'idx_role_unions_user_uid_' . $i);
                        $table->index('role_id', 'idx_role_unions_role_id_' . $i);
                        $table->index('created_time', 'idx_role_unions_cre_time_' . $i);
                        $table->index('updated_time', 'idx_role_unions_upd_time_' . $i);
                    });

                    $prefix = config('database.connections.'.$dbConnection.'.prefix');
                    DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}'");
                }
            }
        } else {
            if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
                Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                    $table->id()->comment('主键');
                    $table->unsignedBigInteger('user_role_union_uid')->comment('用户角色关联雪花ID');
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedInteger('role_id')->default(0)->comment('角色id');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_role_union_uid', 'uni_role_unions_ur_uid');
                    $table->index('user_uid', 'idx_role_unions_user_uid');
                    $table->index('role_id', 'idx_role_unions_role_id');
                    $table->index('created_time', 'idx_role_unions_cre_time');
                    $table->index('updated_time', 'idx_role_unions_upd_time');
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable}` comment '{$this->tableComment}'");
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
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        if ($this->hasSnowflake) {
            for ($i = 0; $i < $tableCount; $i++) {
                $tableName = $this->baseTable . '_' . $i;
                if (Schema::connection($dbConnection)->hasTable($tableName)) {
                    Schema::connection($dbConnection)->dropIfExists($tableName);
                }
            }
        } else {
            if (Schema::connection($dbConnection)->hasTable($this->baseTable)) {
                Schema::connection($dbConnection)->dropIfExists($this->baseTable);
            }
        }
    }
};
