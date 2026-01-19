<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-23 16:59:14
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:26:48
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

        if (!Schema::connection($db_connection)->hasTable('user_pictures')) {
            Schema::connection($db_connection)->create('user_pictures', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_picture_uid', 20)->comment('用户图片雪花ID');
                $table->char('user_uid', 20)->default('')->comment('用户uid');
                $table->char('album_picture_uid', 20)->default('')->comment('相册图片uid,雪花ID');
                $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认 0否 1是');
                $table->unsignedTinyInteger('type')->default(0)->comment('图片类型');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->unique('user_picture_uid', 'uni_user_pictures_uid');
                $table->index('user_uid', 'idx_user_pictures_user_uid');
                $table->index('album_picture_uid', 'idx_user_pictures_picture_uid');
                $table->index('created_time', 'idx_user_pictures_created_time');
                $table->index('is_default', 'idx_user_pictures_is_default');
                $table->index('sort', 'idx_user_pictures_sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_pictures` comment '用户图片表'");
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

        if (Schema::connection($db_connection)->hasTable('user_pictures')) {
            Schema::connection($db_connection)->dropIfExists('user_pictures');
        }
    }
};
