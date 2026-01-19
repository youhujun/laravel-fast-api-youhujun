<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 10:12:23
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 14:13:44
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

        if (!Schema::connection($db_connection)->hasTable('user_avatars')) {
            Schema::connection($db_connection)->create('user_avatars', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_avatar_uid', 20)->comment('用户头像雪花ID');
                $table->char('user_uid', 20)->default('')->comment('用户uid');
                $table->char('album_picture_uid', 20)->default('')->comment('相册图片uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认使用 0否1是 ');


                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');

                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                //索引
                $table->unique('user_avatar_uid', 'uni_user_avatars_uid');
                $table->index('user_uid', 'idx_user_avatars_user_uid');
                $table->index('album_picture_uid', 'idx_user_avatars_picture_uid');
                $table->index('created_time', 'idx_user_avatars_created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_avatars` comment '用户头像表'");
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

        if (Schema::connection($db_connection)->hasTable('user_avatars')) {
            Schema::connection($db_connection)->dropIfExists('user_avatars');
        }
    }
};
