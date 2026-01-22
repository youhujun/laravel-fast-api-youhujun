<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-17 09:25:11
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:52:00
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
        if (!Schema::connection($db_connection)->hasTable('album_pictures')) {
            Schema::connection($db_connection)->create('album_pictures', function (Blueprint $table) {
                $table->id()->comment('主键');

                // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                $table->unsignedBigInteger('album_picture_uid')->comment('相册图片全局唯一ID,雪花ID,业务核心ID');

                $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键：user_id%100/ID%100，未来分库分表用');
                $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid,雪花ID');
                $table->unsignedBigInteger('album_uid')->default(0)->comment('相册uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                $table->string('picture_name', 64)->default('')->comment('图片名称');
                $table->string('picture_tag', 64)->default('')->comment('图片标签');
                $table->string('picture_path', 128)->default('')->comment('图片路径');
                $table->string('picture_file', 64)->default('')->comment('图片文件');
                $table->unsignedInteger('picture_size')->default(0)->comment('图片大小(kb)');
                $table->string('picture_spec', 64)->default('')->comment('图片规格(长*宽)');

                $table->unsignedTinyInteger('picture_type')->default(0)->comment('图片类型 10 本地 20存储桶 30微信头像');
                $table->string('picture_url', 255)->default('')->comment('图片地址(存放于存储桶中)');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('album_picture_uid', 'uni_album_pictures_ap_uid');
                $table->index('admin_uid', 'idx_album_pictures_admin_uid');
                $table->index('user_uid', 'idx_album_pictures_user_uid');
                $table->index('album_uid', 'idx_album_pictures_album_uid');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}album_pictures` comment '用户相册图片表'");
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
        if (Schema::connection($db_connection)->hasTable('album_pictures')) {
            Schema::connection($db_connection)->dropIfExists('album_pictures');
        }
    }
};
