<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-08-24 10:14:38
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    protected $baseTable = 'goods_class_unions';
    protected $hasSnowflake = false;
    protected $tableComment = '商品与分类关联表';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {

                $table->id()->comment('主键');
                $table->char('goods_uid', 20)->comment('商品雪花ID');
                $table->unsignedBigInteger('goods_class_id')->default(0)->comment('分类id');
                $table->unsignedBigInteger('goods_class_one_depp_id')->default(0)->comment('一级分类id');
                $table->unsignedBigInteger('goods_class_two_depp_id')->default(0)->comment('二级分类id');
                $table->unsignedBigInteger('goods_class_three_depp_id')->default(0)->comment('三级分类id');
                $table->unsignedBigInteger('goods_class_four_depp_id')->default(0)->comment('四级分类id');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique('goods_uid', 'uni_goods_cls_uns_goods_uid');
                $table->index('goods_class_id', 'idx_goods_cls_uns_cls_id');
                $table->index('goods_class_one_depp_id', 'idx_goods_cls_uns_one_depp_id');
                $table->index('goods_class_two_depp_id', 'idx_goods_cls_uns_two_depp_id');
                $table->index('goods_class_three_depp_id', 'idx_goods_cls_uns_thr_depp_id');
                $table->index('goods_class_four_depp_id', 'idx_goods_cls_uns_fou_depp_id');
                $table->index('created_time', 'idx_goods_cls_uns_cre_time');

            });

            $prefix = config('database.connections.'.$dbConnection.'.prefix');

            DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$this->baseTable}` comment '{$this->tableComment}'");
        }

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }

    }
};
