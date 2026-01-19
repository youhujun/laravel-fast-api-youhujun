<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-01-04 09:56:40
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:45:14
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
        if (!Schema::connection($db_connection)->hasTable('articles')) {
            Schema::connection($db_connection)->create('articles', function (Blueprint $table) {
                $table->id()->comment('主键');

                // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                $table->char('article_uid', 20)->notNull()->comment('文章全局唯一ID,雪花ID,业务核心ID');

                $table->char('admin_uid', 20)->notNull()->default('')->comment('管理员uid,雪花ID');
                $table->char('user_uid', 20)->notNull()->default('')->comment('发布人uid,雪花ID');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->string('title', 64)->notNull()->default('')->comment('文章标题');
                $table->unsignedTinyInteger('status')->notNull()->default(0)->comment('状态 默认0 0未发布 10已发布');
                $table->unsignedTinyInteger('type')->notNull()->default(0)->comment('文章类型 默认0 0无 10公告通知');
                $table->unsignedTinyInteger('is_top')->notNull()->default(0)->comment('是否置顶 默认0 0不置顶 1置顶');
                $table->unsignedTinyInteger('check_status')->notNull()->default(0)->comment('审核状态 默认0 0 待审核 10 审核中 20审核通过 30审核不通过');

                $table->string('category_id', 255)->nullable()->comment('文章分类json');
                $table->string('label_id', 255)->nullable()->comment('标签分类json');
                $table->dateTime('published_at')->nullable()->comment('发布时间string');
                $table->unsignedInteger('published_time')->notNull()->default(0)->comment('发布时间int');
                $table->dateTime('checked_at')->nullable()->comment('审核时间string');
                $table->unsignedInteger('checked_time')->notNull()->default(0)->comment('审核时间int');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序 默认100');

                // 索引

                $table->unique('article_uid', 'uni_articles_article_uid');

                $table->index('admin_uid', 'idx_articles_admin_uid');
                $table->index('user_uid', 'idx_articles_user_uid');
                $table->index('type', 'idx_articles_type');
                $table->index('is_top', 'idx_articles_is_top');
                $table->index('check_status', 'idx_articles_chk_status');
                $table->index('sort', 'idx_articles_sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}articles` comment '文章表'");
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
        if (Schema::connection($db_connection)->hasTable('articles')) {
            Schema::connection($db_connection)->dropIfExists('articles');
        }
    }
};
