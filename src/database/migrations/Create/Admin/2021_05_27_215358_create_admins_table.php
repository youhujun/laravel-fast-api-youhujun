<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-27 21:53:58
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:43:53
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
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');


                $table->char('admin_uid', 20)->notNull()->default('')->comment('管理员全局唯一ID,雪花ID,业务核心ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户全局唯一ID,雪花ID,业务核心ID');

                $table->string('role_id', 255)->notNull()->default('')->comment('管理员角色id集合');
                $table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否|开关 0关否1是开');
                $table->string('remember_token', 128)->nullable()->comment('记住token');
                $table->string('account_name', 64)->nullable()->comment('账户名称 唯一');
                $table->char('phone_area_code', 5)->notNull()->default('')->comment('手机号区号');
                $table->char('phone', 15)->unique()->nullable()->comment('手机号');
                $table->string('password', 255)->notNull()->default('')->comment('密码');
                $table->string('email', 128)->unique()->nullable()->comment('邮箱');
                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('admin_uid');
                $table->unique(['account_name', 'deleted_at']);
                $table->unique(['phone', 'deleted_at']);
                $table->unique(['email', 'deleted_at']); // 邮箱+软删除字段复合唯一

                $table->index('user_uid');
                $table->index('switch');
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
