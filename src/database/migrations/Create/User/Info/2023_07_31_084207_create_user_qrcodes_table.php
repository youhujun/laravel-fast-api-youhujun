<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-07-31 08:42:07
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 11:26:34
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_qrcodes';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户二维码表';

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

                    $table->unsignedBigInteger('user_qrcode_uid')->comment('用户二维码雪花ID');

                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');

                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('album_picture_uid')->default(0)->comment('相册图片uid,雪花ID');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认使用 0否1是 ');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');
                    $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');

                    // 索引
                    $table->unique('user_qrcode_uid', 'uni_user_qrcodes_uid_' . $i);
                    $table->index('user_uid', 'idx_user_qrcodes_user_uid_' . $i);
                    $table->index('album_picture_uid', 'idx_user_qrcodes_picture_uid_' . $i);
                    $table->index('created_time', 'idx_user_qrcodes_created_time_' . $i);
                    $table->index('data_type', 'idx_user_qrcodes_data_type_' . $i);
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
