<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-20 17:09:03
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_banks';
    protected $hasSnowflake = true;
    protected $tableComment = '用户银行信息表';

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
                    $table->id()->comment('主键 用户银行信息表');
                    $table->unsignedBigInteger('user_bank_uid')->comment('用户银行卡雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedInteger('bank_id')->default(0)->comment('银行id');
                    $table->unsignedBigInteger('bank_front_uid')->default(0)->comment('银行卡正面(相册图片雪花ID)');
                    $table->unsignedBigInteger('bank_back_uid')->default(0)->comment('银行卡背面(相册图片雪花ID)');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认 0不 1是');
                    $table->string('bank_number', 32)->default('')->comment('银行卡号');
                    $table->string('bank_account', 32)->default('')->comment('银行户名');
                    $table->string('bank_address', 128)->default('')->comment('开户行地址');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_bank_uid', 'uni_user_banks_uid_' . $i);
                    $table->index('user_uid', 'idx_user_banks_user_uid_' . $i);
                    $table->index('bank_id', 'idx_user_banks_bank_id_' . $i);
                    $table->index('created_time', 'idx_user_banks_created_time_' . $i);
                    $table->index('bank_front_uid', 'idx_user_banks_bank_front_uid_' . $i);
                    $table->index('bank_back_uid', 'idx_user_banks_bank_back_uid_' . $i);
                    $table->index('is_default', 'idx_user_banks_is_default_' . $i);
                    $table->index('sort', 'idx_user_banks_sort_' . $i);
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
