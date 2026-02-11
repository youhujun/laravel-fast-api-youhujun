<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-03-09 11:25:55
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:48:06
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'job_batches';
    protected $hasSnowflake = false;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = '';
    protected $tableComment = '批处理队列表';

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
        if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->string('id')->primary()->comment('批处理任务表主键');
                $table->string('name')->default('')->comment('名称');
                $table->integer('total_jobs')->default(0)->comment('任务总数');
                $table->integer('pending_jobs')->default(0)->comment('等待任务数');
                $table->integer('failed_jobs')->default(0)->comment('失败任务数');
                $table->text('failed_job_ids')->default('')->comment('失败任务标识');
                $table->mediumText('options')->nullable()->comment('选项');
                $table->integer('cancelled_at')->nullable()->comment('取消时间');
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->integer('finished_at')->nullable()->comment('完成时间');
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
        if (Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }
    }
};
