<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-07-12 10:52:49
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 04:22:43
 * @FilePath: \youhu-laravel-api-12d:\wwwroot\PHP\Components\Laravel\youhujun\laravel-fast-api-youhujun\src\database\migrations\Create\User\Platform\2023_07_12_105249_create_user_wechat_unionids_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_wechat_unionids';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户微信的unionid';

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
                    $table->unsignedBigInteger('user_wechat_unionid_uid')->default(0)->comment('用户微信unionid雪花ID');

                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');

                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->string('unionid', 64)->nullable()->comment('微信的unionid 唯一');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_wechat_unionid_uid', 'uni_wechat_uids_uw_uid_' . $i);
                    $table->unique('unionid', 'uni_wechat_uids_unionid_' . $i);
                    $table->index('user_uid', 'idx_wechat_uids_user_uid_' . $i);
                    $table->index('created_time', 'idx_wechat_uids_cre_time_' . $i);
                    $table->index('sort', 'idx_wechat_uids_sort_' . $i);
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
