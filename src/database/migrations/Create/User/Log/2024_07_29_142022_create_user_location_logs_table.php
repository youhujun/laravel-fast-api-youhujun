<?php

/*
 * @Descripttion: 用户位置信息记录表
 * @version:
 * @Author: YouHuJun
 * @Date: 2024-07-29 14:20:22
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    protected $baseTable = 'user_location_logs';
    protected $hasSnowflake = true;
    protected $tableComment = '用户位置信息记录表';

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
            if (!Schema::connection($dbConnection)->hasTable($tableName))
            {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    $table->unsignedBigInteger('user_location_log_uid')->comment('日志uid,雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->tinyInteger('type')->default(0)->comment('类型 10用户');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->decimal('latitude',32,10,true)->default(0)->comment('维度');
                    $table->decimal('longitude',32,10,true)->default(0)->comment('经度');
                    $table->string('address',128)->default('')->comment('位置信息');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_location_log_uid', 'uni_user_location_logs_uid_' . $i);
                    $table->index('user_uid', 'idx_user_location_logs_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_location_logs_created_time_' . $i);
                    $table->index('sort', 'idx_user_location_logs_sort_' . $i);
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
            if (Schema::connection($dbConnection)->hasTable($tableName))
            {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }

    }
};
