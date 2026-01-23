<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-23 17:42:17
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-14 09:19:56
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'admin_event_logs';
    protected $hasSnowflake = false;
    protected $tableComment = '管理员事件日志';

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
                $table->unsignedBigInteger('admin_event_log_uid')->comment('日志uid,雪花ID');
                $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                $table->unsignedInteger('event_type')->default(0)->comment('事件类型');
                $table->string('event_route_action',128)->default('')->comment('事件路由');
                $table->string('event_name',64)->default('')->comment('事件名称');
                $table->string('event_code',64)->default('')->comment('事件编码');
                $table->text('note')->nullable()->comment('备注数据');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('admin_event_log_uid', 'uni_admin_event_logs_log_uid');
                $table->index('admin_uid', 'idx_admin_event_logs_admin_uid');
                $table->index('event_type', 'idx_admin_event_logs_event_type');
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
