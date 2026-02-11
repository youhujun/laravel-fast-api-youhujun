<?php

/*
 * @Descripttion: 用户余额表
 * @version:
 * @Author: YouHuJun
 * @Date: 2025-07-10 21:00:26
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 11:26:44
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_amounts';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户余额表';

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
                    // 分片键：user_uid%100，未来分库分表用
                    $table->unsignedBigInteger('user_amount_uid')->comment('用户余额雪花ID');
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->decimal('amount', 32, 8)->default(0)->comment('余额');
                    $table->decimal('bonus', 32, 8)->default(0)->comment('奖金');
                    $table->decimal('prepare_bonus', 32, 8)->default(0)->comment('预计增加奖金');
                    $table->decimal('coin', 32, 8)->default(0)->comment('系统币');
                    $table->decimal('score', 32, 8)->default(0)->comment('积分');

                    $table->string('note', 128)->default('')->comment('备注');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_amount_uid', 'uni_user_amounts_uid_' . $i);
                    $table->index('user_uid', 'idx_user_amounts_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_amounts_created_time_' . $i);
                    $table->index('sort', 'idx_user_amounts_sort_' . $i);
                });

                //注意是否需要修改mysql连接名
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
