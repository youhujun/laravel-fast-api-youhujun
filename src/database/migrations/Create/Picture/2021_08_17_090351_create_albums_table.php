<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-17 09:03:51
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:29:14
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
        if (!Schema::connection($db_connection)->hasTable('albums')) {
            Schema::connection($db_connection)->create('albums', function (Blueprint $table) {
                $table->id()->comment('主键');

                // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                $table->char('album_uid', 20)->notNull()->comment('相册全局唯一ID,雪花ID,业务核心ID');

                $table->char('admin_uid', 20)->notNull()->default('')->comment('管理员uid,雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid,雪花ID');
                $table->char('cover_album_picture_uid', 20)->notNull()->default('')->comment('封面相册图片uid,雪花ID');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('is_default')->notNull()->default(0)->comment('筛选默认相册 0否 1是');
                $table->unsignedTinyInteger('is_system')->notNull()->default(0)->comment('系统分配默认相册 不可删除 0否 1是');
                $table->unsignedTinyInteger('album_type')->notNull()->default(20)->comment('相册类型 默认20用户 0系统,10管理员');
                $table->string('album_name', 64)->notNull()->default('')->comment('相册名称');
                $table->string('album_description', 255)->notNull()->default('')->comment('相册描述');
                $table->unsignedTinyInteger('sort')->notNull()->default(0)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->unique('album_uid');
                $table->index('admin_uid');
                $table->index('user_uid');
                $table->index('cover_album_picture_uid');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}albums` comment '相册表'");
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
        if (Schema::connection($db_connection)->hasTable('albums')) {
            Schema::connection($db_connection)->dropIfExists('albums');
        }
    }
};
