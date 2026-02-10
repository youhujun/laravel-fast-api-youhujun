<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-12-25 09:57:23
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 * @FilePath: \database\migrations\2024_12_25_095723_create_user_douyin_unionid_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_douyin_unionids';
    protected $hasSnowflake = true;
    protected $tableComment = '用户抖音的unionid';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    $table->id()->comment('主键');
                    $table->unsignedBigInteger('user_douyin_unionid_uid')->default(0)->comment('用户抖音unionid雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->string('unionid', 64)->nullable()->comment('抖音unionid 唯一');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_douyin_unionid_uid', 'uni_douyin_uids_ud_uid_' . $i);
                    $table->unique('unionid', 'uni_douyin_uids_unionid_' . $i);
                    $table->index('user_uid', 'idx_douyin_uids_user_uid_' . $i);
                    $table->index('created_time', 'idx_douyin_uids_cre_time_' . $i);
                    $table->index('sort', 'idx_douyin_uids_sort_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');

                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
            }
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
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
