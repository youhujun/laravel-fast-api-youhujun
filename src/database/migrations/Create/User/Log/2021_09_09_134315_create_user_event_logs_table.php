<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-09-09 13:43:15
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2026-01-23 21:05:57
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_event_logs';
    protected $hasSnowflake = false;
    protected $tableComment = '用户事件日志表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->unsignedBigInteger('user_event_log_uid')->comment('日志uid,雪花ID');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                $table->unsignedInteger('event_type')->default(0)->comment('事件类型');
                $table->string('event_route_action',128)->default('')->comment('事件路由');
                $table->string('event_name',64)->default('')->comment('事件名称');
                $table->string('event_code',64)->default('')->comment('事件编码');
                $table->text('note')->nullable()->comment('备注数据');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_event_log_uid', 'uni_user_event_logs_uid');
                $table->index('user_uid', 'idx_user_event_logs_user_uid');
                $table->index('event_type', 'idx_user_event_logs_event_type');
                $table->index('created_time', 'idx_user_event_logs_created_time');
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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }


    }
};
