<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-04-03 09:17:09
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:25:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_real_auth_logs';
    protected $hasSnowflake = true;
    protected $tableComment = '用户实名认证日志表';

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
                    $table->unsignedBigInteger('user_real_auth_log_uid')->comment('日志uid,雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('admin_uid')->default(0)->comment('审核的管理员id');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');
                    $table->unsignedTinyInteger('status')->default(0)->comment('状态 10申请中 20通过 30拒绝');
                    $table->dateTime('auth_apply_at')->nullable()->comment('实名认证申请时间string');
                    $table->unsignedInteger('auth_apply_time')->default(0)->comment('实名认证申请时间int');
                    $table->dateTime('auth_at')->nullable()->comment('实名认证审核时间string');
                    $table->unsignedInteger('auth_time')->default(0)->comment('实名认证审核时间int');
                    $table->string('refuse_info', 64)->default('')->comment('拒绝原因');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_real_auth_log_uid', 'uni_user_real_auth_logs_uid_' . $i);
                    $table->index('user_uid', 'idx_user_real_auth_logs_user_uid_' . $i);
                    $table->index('auth_apply_time', 'idx_user_real_auth_logs_apply_' . $i);
                    $table->index('created_time', 'idx_user_real_auth_logs_created_' . $i);
                    $table->index('auth_time', 'idx_user_real_auth_logs_auth_' . $i);
                    $table->index('sort', 'idx_user_real_auth_logs_sort_' . $i);
                    $table->index('status', 'idx_user_real_auth_logs_status_' . $i);
                    $table->index('data_type', 'idx_user_real_auth_logs_data_type_' . $i);
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
