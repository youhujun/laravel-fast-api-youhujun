<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-10-18 09:01:47
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:41:48
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
        if (!Schema::connection($db_connection)->hasTable('article_categories')) {
            Schema::connection($db_connection)->create('article_categories', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedInteger('parent_id')->notNull()->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('级别');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('switch')->notNull()->default(0)->comment('是否可用 0否1是');
                $table->decimal('rate', 4, 2)->notNull()->default(0)->comment('分润比例%');
                $table->string('category_name', 64)->nullable()->comment('分类名称');
                $table->string('category_code', 64)->nullable()->comment('分类逻辑名称');
                $table->char('category_picture_uid', 20)->notNull()->default('')->comment('分类图片雪花ID(相册图片id)');
                $table->string('note', 255)->nullable()->comment('备注说明');
                $table->unsignedTinyInteger('sort')->notNull()->default(0)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->index('parent_id');
                $table->index('deep');
                $table->index('switch');
                $table->unique(['category_name','deleted_at']);
                $table->unique(['category_code','deleted_at']);
                $table->index('category_picture_uid');
                $table->index('created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}article_categories` comment '文章分类表'");
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

        if (Schema::connection($db_connection)->hasTable('article_categories')) {
            Schema::connection($db_connection)->dropIfExists('article_categories');
        }
    }
};
