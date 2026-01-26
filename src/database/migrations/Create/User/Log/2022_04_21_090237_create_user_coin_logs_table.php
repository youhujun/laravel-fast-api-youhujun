<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-21 09:02:37
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:25:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_coin_logs';
    protected $hasSnowflake = true;
    protected $tableComment = '用户系统币日志表';

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
                    $table->unsignedBigInteger('user_coin_log_uid')->comment('日志uid,雪花ID');
                    // 分片键：user_uid%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');
                    $table->decimal('before_amount', 32, 8)->default(0)->comment('金额');
                    $table->decimal('change_value', 32, 8)->default(0)->comment('变动数值');
                    $table->unsignedTinyInteger('change_type')->default(0)->comment('0未知 10充值 20 支出 30退款');
                    $table->decimal('amount', 32, 8)->default(0)->comment('系统币');
                    $table->string('note', 32)->nullable()->comment('备注');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_coin_log_uid', 'uni_user_coin_logs_uid_' . $i);
                    $table->index('user_uid', 'idx_user_coin_logs_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_coin_logs_created_time_' . $i);
                    $table->index('change_type', 'idx_user_coin_logs_change_type_' . $i);
                    $table->index('data_type', 'idx_user_coin_logs_data_type_' . $i);
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
