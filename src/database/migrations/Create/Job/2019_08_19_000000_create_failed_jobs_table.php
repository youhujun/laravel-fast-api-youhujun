<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-23 15:35:15
 * @LastEditors: YouHuJun
 * @LastEditTime: 2026-01-23 21:05:57
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'failed_jobs';
    protected $hasSnowflake = false;
	// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = '';
    protected $tableComment = '失败队列表';

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
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                $table->dateTime('failed_at')->useCurrent()->comment('失败时间');
                $table->unsignedInteger('failed_time')->default(0)->comment('失败时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                $table->string('uuid',100)->comment('唯一标识');
                $table->text('connection')->comment('连接');
                $table->text('queue')->comment('队列');
                $table->longtext('payload')->comment('有效载荷');
                $table->longtext('exception')->nullable()->comment('异常');

                // 索引
                $table->unique('uuid', 'uni_failed_jobs_uuid');
                $table->index('failed_time', 'idx_failed_jobs_failed_time');

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
