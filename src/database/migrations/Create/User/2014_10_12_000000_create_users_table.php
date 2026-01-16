<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-23 15:35:15
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:58:32
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');

        if (!Schema::connection($db_connection)->hasTable('users')) {
            Schema::connection($db_connection)->create('users', function (Blueprint $table) {
                // 物理自增主键（仅数据库层面用，业务代码不碰）
                $table->id()->comment('物理主键（自增）');
                // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                $table->char('user_uid', 20)->notNull()->comment('用户全局唯一ID,雪花ID,业务核心ID');
                // 关联字段同步改为 uid 后缀，保持命名一致
                $table->char('source_user_uid', 20)->notNull()->default('')->comment('推荐人全局唯一ID');

                $table->char('parent_user_uid', 20)->notNull()->default('')->comment('父级全局唯一ID');

                // 状态字段
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('account_status')->notNull()->default(1)->comment('账户状态 0禁用 1启用');
                $table->unsignedTinyInteger('real_auth_status')->notNull()->default(10)->comment('实名认证状态 10未认证 20认证中 30未通过 40通过');
                $table->unsignedTinyInteger('level_id')->notNull()->default(0)->comment('用户级别ID');
                $table->unsignedTinyInteger('source')->notNull()->default(0)->comment('注册来源 10 H5 20抖音 30微信');

                // 认证/登录字段（长度优化）
                $table->string('remember_token', 128)->nullable()->comment('记住登录token');
                $table->string('auth_token', 128)->nullable()->comment('认证token');
                $table->string('account_name', 64)->nullable()->comment('账户名称（唯一）');
                $table->char('invite_code', 7)->nullable()->comment('唯一邀请码4-7位纯小写字母+数字，用户可见');
                $table->char('phone_area_code', 5)->notNull()->default('')->comment('手机号区号如+86');
                $table->char('phone', 15)->nullable()->comment('手机号');
                $table->char('password', 60)->notNull()->default('')->comment('密码bcrypt哈希,固定60位');
                $table->string('email', 128)->nullable()->comment('邮箱');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引 - 核心修改部分
                $table->unique('user_uid');
                $table->unique('remember_token');
                $table->unique('auth_token');
                // 适配软删除）
                $table->unique(['account_name', 'deleted_at']);
                $table->unique(['invite_code', 'deleted_at']);
                $table->unique(['phone', 'deleted_at']); // 手机号+软删除字段复合唯一
                $table->unique(['email', 'deleted_at']); // 邮箱+软删除字段复合唯一
                $table->index('source_user_uid');
                $table->index('parent_user_uid');
                $table->index('created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}users` comment '用户表'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $db_connection = config('youhujun.db_connection');

        if (Schema::connection($db_connection)->hasTable('users')) {
            Schema::connection($db_connection)->dropIfExists('users');
        }
    }
};
