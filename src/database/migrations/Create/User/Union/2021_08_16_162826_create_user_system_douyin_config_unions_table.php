<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 16:28:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_system_douyin_config_unions';
    protected $hasSnowflake = true;
    protected $tableComment = '用户和系统抖音配置关联表openid';

    /**
     * Run migrations.
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
                    $table->unsignedBigInteger('user_system_douyin_config_union_uid')->default(0)->comment('用户抖音配置关联雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->string('openid', 100)->nullable()->comment('唯一openid');
                    $table->string('session_key', 100)->default('')->comment('session_key');
                    $table->unsignedInteger('system_douyin_config_id')->default(0)->comment('系统抖音配置id');
                    $table->dateTime('verified_at')->nullable()->comment('微信号认证时间string');
                    $table->unsignedInteger('verified_time')->default(0)->comment('微信号认证时间int');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_system_douyin_config_union_uid', 'uni_sys_douyin_usd_uid_' . $i);
                    $table->unique('openid', 'uni_sys_douyin_openid_' . $i);
                    $table->index('user_uid', 'idx_sys_douyin_user_uid_' . $i);
                    $table->index('created_time', 'idx_sys_douyin_cre_time_' . $i);
                    $table->index('verified_time', 'idx_sys_douyin_ver_time_' . $i);
                    $table->index('system_douyin_config_id', 'idx_sys_douyin_sys_did_' . $i);
                    $table->index('updated_time', 'idx_sys_douyin_upd_time_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');

                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
            }
        }
    }

    /**
     * Reverse migrations.
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
