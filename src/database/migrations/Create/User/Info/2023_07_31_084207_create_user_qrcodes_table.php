<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-07-31 08:42:07
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:30:42
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

        if (!Schema::connection($db_connection)->hasTable('user_qrcodes')) {
            Schema::connection($db_connection)->create('user_qrcodes', function (Blueprint $table) {
                $table->id()->comment('主键');

                $table->unsignedBigInteger('user_qrcode_uid')->comment('用户二维码雪花ID');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                $table->unsignedBigInteger('album_picture_uid')->default(0)->comment('相册图片uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认使用 0否1是 ');
                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');
                //索引
                $table->unique('user_qrcode_uid', 'uni_user_qrcodes_uid');
                $table->index('user_uid', 'idx_user_qrcodes_user_uid');
                $table->index('album_picture_uid', 'idx_user_qrcodes_picture_uid');
                $table->index('created_time', 'idx_user_qrcodes_created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_qrcodes` comment '用户二维码表'");
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

        if (Schema::connection($db_connection)->hasTable('user_qrcodes')) {
            Schema::connection($db_connection)->dropIfExists('user_qrcodes');
        }
    }
};
