<?php

/*
 * @Descripttion: 游鹄生态-用户分表迁移文件
 * @version: 1.0
 * @Author: YouHuJun
 * @Date: 2021-05-23 15:35:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-12 17:37:28
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    /**
     * 
     */
    protected $baseTable = 'users';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户表'; // 改注释


    /**
     * Run the migrations.
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
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) { // 关键：把$i传入闭包
                    // 物理自增主键（仅数据库层面用，业务代码不碰）
                    $table->id()->comment('物理主键（自增）');
                    // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                    $table->unsignedBigInteger('user_uid')->comment('用户全局唯一ID,雪花ID,业务核心ID');
                    // 分片键：user_id%100/ID%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    // 关联字段同步改为 uid 后缀，保持命名一致
                    $table->unsignedBigInteger('source_user_uid')->default(0)->comment('推荐人全局唯一ID');
                    $table->unsignedBigInteger('parent_user_uid')->default(0)->comment('父级全局唯一ID');

                    // 状态字段
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('account_status')->default(1)->comment('账户状态 0禁用 1启用');
                    $table->unsignedTinyInteger('real_auth_status')->default(10)->comment('实名认证状态 10未认证 20认证中 30未通过 40通过');
                    $table->unsignedTinyInteger('level_id')->default(0)->comment('用户级别ID');
                    $table->unsignedTinyInteger('source')->default(0)->comment('注册来源 10 H5 20抖音 30微信');

                    // 认证/登录字段（长度优化）
                    $table->string('remember_token', 128)->nullable()->comment('记住登录token');
                    $table->char('auth_token', 36)->nullable()->comment('认证token');
                    $table->string('account_name', 64)->nullable()->comment('账户名称（唯一）');
                    $table->char('invite_code', 7)->nullable()->comment('唯一邀请码4-7位纯小写字母+数字，用户可见');
                    $table->char('phone_area_code', 5)->default('')->comment('手机号区号如+86');
                    $table->char('phone', 15)->nullable()->comment('手机号');
                    $table->char('password', 60)->default('')->comment('密码bcrypt哈希,固定60位');
                    $table->string('email', 128)->nullable()->comment('邮箱');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                    // 索引 - 关键修复：加$i区分分表索引名，避免重复
                    $table->unique('user_uid', 'uni_users_user_uid_' . $i);
                    $table->unique('remember_token', 'uni_users_remember_token_' . $i);
                    $table->unique('auth_token', 'uni_users_auth_token_' . $i);
                    // 适配软删除
                    $table->unique(['account_name', 'deleted_at'], 'uni_users_account_name_deleted_' . $i);

                    $table->unique(['auth_token', 'deleted_at'], 'uni_users_auth_token_deleted_' . $i);

                    $table->unique(['invite_code', 'deleted_at'], 'uni_users_invite_code_deleted_' . $i);
                    $table->unique(['phone', 'deleted_at'], 'uni_users_phone_deleted_' . $i);
                    $table->unique(['email', 'deleted_at'], 'uni_users_email_deleted_' . $i);
                    $table->index('source_user_uid', 'idx_users_source_user_uid_' . $i);
                    $table->index('parent_user_uid', 'idx_users_parent_user_uid_' . $i);
                    $table->index('created_time', 'idx_users_created_time_' . $i);
                });

                // 关键修复：补全单引号，修正SQL语法
                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
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
