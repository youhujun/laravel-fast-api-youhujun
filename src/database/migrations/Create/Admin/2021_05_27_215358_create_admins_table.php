<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-27 21:53:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-22 11:32:35
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    /**
     * 基础表名（和ShardFacade::getTableName的baseTable对齐）
     */
    protected $baseTable = 'admins';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '管理员表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db']; // 读取ds_0
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i; // 对齐ShardFacade::getTableName规则
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    // 物理自增主键（仅数据库层面用，业务代码不碰）
                    $table->id()->comment('物理主键（自增）');
                    // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                    $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员全局唯一ID,雪花ID,业务核心ID');
                    // 分片键：user_id%100/ID%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    // 关联字段同步改为 uid 后缀，保持命名一致
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户全局唯一ID');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                    // 状态字段
                    $table->unsignedTinyInteger('account_status')->default(1)->comment('账户状态 0禁用 1启用');

                    // 认证/登录字段（长度优化）
                    $table->string('remember_token', 128)->nullable()->comment('记住登录token');
                    $table->string('account_name', 64)->nullable()->comment('账户名称（唯一）');
                    $table->char('phone_area_code', 5)->default('')->comment('手机号区号如+86');
                    $table->char('phone', 15)->nullable()->comment('手机号');
                    $table->string('password', 255)->default('')->comment('密码bcrypt哈希');
                    $table->string('email', 128)->nullable()->comment('邮箱');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                    // 索引 - 关键修复：加$i区分分表索引名，避免重复
                    $table->unique('admin_uid', 'uni_admins_admin_uid_' . $i);
                    // 适配软删除
                    $table->unique(['account_name', 'deleted_at'], 'uni_admins_account_name_deleted_' . $i);
                    $table->unique(['phone', 'deleted_at'], 'uni_admins_phone_deleted_' . $i);
                    $table->unique(['email', 'deleted_at'], 'uni_admins_email_deleted_' . $i);
                    $table->index('user_uid', 'idx_admins_user_uid_' . $i);
                    $table->index('account_status', 'idx_admins_account_status_' . $i);
                    $table->index('created_time', 'idx_admins_created_time_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}'");
            }
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
        $dbConnection = $shardConfig['default_db']; // 读取ds_0

        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
