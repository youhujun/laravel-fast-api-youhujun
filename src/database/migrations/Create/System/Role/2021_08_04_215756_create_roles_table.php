<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-13 14:58:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 12:01:36
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

        if (!Schema::connection($db_connection)->hasTable('roles')) {
            Schema::connection($db_connection)->create('roles', function (Blueprint $table) {
                $table->increments('id')->comment('主键');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedBigInteger('parent_id')->notNull()->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('深度级别');
                $table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否|开关 0关否1是开');
                $table->string('role_name', 32)->notNull()->default('')->comment('角色名称');
                $table->string('logic_name', 64)->notNull()->default('')->comment('逻辑名称');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');



                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->unique(['role_name','deleted_at']);
                $table->unique(['logic_name','deleted_at']);
                $table->index('parent_id');
                $table->index('deep');
                $table->index('switch');
                $table->index('sort');
                $table->index('created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}roles` comment '角色表'");
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

        if (Schema::connection($db_connection)->hasTable('roles')) {
            Schema::connection($db_connection)->dropIfExists('roles');
        }
    }
};
