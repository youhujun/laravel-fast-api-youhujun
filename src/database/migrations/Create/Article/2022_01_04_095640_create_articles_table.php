<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-01-04 09:56:40
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-10 23:01:13
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'articles';
    protected $hasSnowflake = true;
    protected $tableComment = '文章表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db']; // 读取ds_0
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i; // 对齐ShardFacade::getTableName规则
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    // 物理自增主键（仅数据库层面用，业务代码不碰）
                    $table->id()->comment('物理主键（自增）');
                    // 雪花ID核心字段（非空+唯一+索引，适配分库分表）
                    $table->unsignedBigInteger('article_uid')->default(0)->comment('文章全局唯一ID,雪花ID,业务核心ID');
                    // 分片键：user_id%100/ID%100，未来分库分表用
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    // 关联字段同步改为 uid 后缀，保持命名一致
                    $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('发布人uid,雪花ID');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                    $table->string('title', 64)->default('')->comment('文章标题');
                    $table->unsignedTinyInteger('status')->default(0)->comment('状态 默认0 0未发布 10已发布');
                    $table->unsignedTinyInteger('type')->default(0)->comment('文章类型 默认0 0无 10公告通知');
                    $table->unsignedTinyInteger('is_top')->default(0)->comment('是否置顶 默认0 0不置顶 1置顶');
                    $table->unsignedTinyInteger('check_status')->default(0)->comment('审核状态 默认0 0 待审核 10 审核中 20审核通过 30审核不通过');

                    $table->string('category_id', 255)->nullable()->comment('文章分类json');
                    $table->string('label_id', 255)->nullable()->comment('标签分类json');
                    $table->dateTime('published_at')->nullable()->comment('发布时间');
                    $table->unsignedInteger('published_time')->default(0)->comment('发布时间戳');
                    $table->dateTime('checked_at')->nullable()->comment('审核时间');
                    $table->unsignedInteger('checked_time')->default(0)->comment('审核时间戳');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                    $table->unsignedTinyInteger('sort')->default(100)->comment('排序 默认100');

                    // 索引 - 关键修复：加$i区分分表索引名，避免重复
                    $table->unique('article_uid', 'uni_articles_article_uid_' . $i);
                    $table->index('admin_uid', 'idx_articles_admin_uid_' . $i);
                    $table->index('user_uid', 'idx_articles_user_uid_' . $i);
                    $table->index('type', 'idx_articles_type_' . $i);
                    $table->index('is_top', 'idx_articles_is_top_' . $i);
                    $table->index('check_status', 'idx_articles_chk_status_' . $i);
                    $table->index('sort', 'idx_articles_sort_' . $i);
                    $table->index('created_time', 'idx_articles_created_time_' . $i);
                });

                // 关键修复：补全单引号，修正SQL语法
                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '游鹄生态-文章分片表_{$i}'");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db']; // 读取ds_0

        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
