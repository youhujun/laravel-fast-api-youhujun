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

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');
        //注意是否需要修改mysql连接名和表名
        if (!Schema::connection($db_connection)->hasTable('admins')) {
            Schema::connection($db_connection)->create('admins', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');


                $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员全局唯一ID,雪花ID,业务核心ID');
                $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键：user_id%100/ID%100，未来分库分表用');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户全局唯一ID,雪花ID,业务核心ID');

                $table->unsignedTinyInteger('account_status')->default(1)->comment('账户状态 0禁用 1启用');
                $table->string('remember_token', 128)->nullable()->comment('记住token');
                $table->string('account_name', 64)->nullable()->comment('账户名称 唯一');
                $table->char('phone_area_code', 5)->default('')->comment('手机号区号');
                $table->char('phone', 15)->unique()->nullable()->comment('手机号');
                $table->string('password', 255)->default('')->comment('密码');
                $table->string('email', 128)->unique()->nullable()->comment('邮箱');
                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('admin_uid', 'uni_admins_admin_uid');
                $table->unique(['account_name', 'deleted_at'], 'uni_admins_account_name_del');
                $table->unique(['phone', 'deleted_at'], 'uni_admins_phone_deleted');
                $table->unique(['email', 'deleted_at'], 'uni_admins_email_deleted'); // 邮箱+软删除字段复合唯一

                $table->index('user_uid', 'idx_admins_user_uid');
                $table->index('account_status', 'idx_admins_account_status');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}admins` comment '管理员表'");
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
        //注意是否需要修改mysql连接名和表名
        if (Schema::connection($db_connection)->hasTable('admins')) {
            Schema::connection($db_connection)->dropIfExists('admins');
        }
    }
};
