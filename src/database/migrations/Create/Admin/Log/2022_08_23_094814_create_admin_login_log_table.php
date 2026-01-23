<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-23 17:48:14
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2026-01-23 21:05:57
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'admin_login_logs';
    protected $hasSnowflake = false;
    protected $tableComment = '管理员登录退出日志表';

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
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->unsignedBigInteger('admin_login_log_uid')->comment('日志uid,雪花ID');
                $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                $table->unsignedTinyInteger('status')->default(0)->comment('状态 0未知 10登录 20退出');
                $table->string('instruction',64)->default('')->comment('说明');
                $table->string('ip',64)->default('')->comment('ip地址');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('admin_login_log_uid', 'uni_admin_login_logs_log_uid');
                $table->index('admin_uid', 'idx_admin_login_logs_admin_uid');
            });

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

        //注意是否需要修改mysql连接名和表名
        if (Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }
    }
};
