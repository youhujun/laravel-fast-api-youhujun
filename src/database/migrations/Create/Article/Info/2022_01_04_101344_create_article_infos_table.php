<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-01-04 10:13:44
 * @LastEditors: YouHuJun
 * @LastEditTime: 2022-01-04 11:06:28
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'article_infos';
    protected $hasSnowflake = true;
    protected $tableComment = '文章详情表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        //注意是否需要修改mysql连接名和表名
        if ($this->hasSnowflake) {
            for ($i = 0; $i < $tableCount; $i++) {
                $tableName = $this->baseTable . '_' . $i;
                if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                    Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                        $table->id()->comment('主键');
                        $table->unsignedBigInteger('article_info_uid')->comment('文章详情雪花ID');
                        $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:article_uid%table_count(工具包自动计算)');
                        $table->unsignedBigInteger('article_uid')->default(0)->comment('文章uid,雪花ID');
                        $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                        $table->text('article_info')->nullable()->comment('文章详情');

                        // 时间字段（自动填充+索引，关键优化）
                        $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                        $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                        $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                        $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                        $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                        // 索引
                        $table->unique('article_info_uid', 'uni_article_infos_info_uid_' . $i);
                        $table->index('article_uid', 'idx_article_infos_article_uid_' . $i);

                    });

                    $prefix = config('database.connections.'.$dbConnection.'.prefix');
                    DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}'");
                }
            }
        } else {
            if (!Schema::connection($dbConnection)->hasTable($this->baseTable))
            {
                Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                    $table->id()->comment('主键');
                    $table->unsignedBigInteger('article_info_uid')->comment('文章详情雪花ID');
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:article_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('article_uid')->default(0)->comment('文章uid,雪花ID');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->text('article_info')->nullable()->comment('文章详情');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                    // 索引
                    $table->unique('article_info_uid', 'uni_article_infos_info_uid');
                    $table->index('article_uid', 'idx_article_infos_article_uid');

                });
            }

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable}` comment '{$this->tableComment}'");
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
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        //注意是否需要修改mysql连接名和表名
        if ($this->hasSnowflake) {
            for ($i = 0; $i < $tableCount; $i++) {
                $tableName = $this->baseTable . '_' . $i;
                if (Schema::connection($dbConnection)->hasTable($tableName)) {
                    Schema::connection($dbConnection)->dropIfExists($tableName);
                }
            }
        } else {
            if (Schema::connection($dbConnection)->hasTable($this->baseTable))
            {
                Schema::connection($dbConnection)->dropIfExists($this->baseTable);
            }

        }

    }
};
