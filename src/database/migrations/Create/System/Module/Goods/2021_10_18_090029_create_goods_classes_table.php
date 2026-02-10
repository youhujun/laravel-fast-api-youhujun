<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-10-18 09:00:29
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:46:21
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'goods_classes';
    protected $hasSnowflake = false;
    protected $tableComment = '产品分类表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedInteger('parent_id')->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->default(0)->comment('级别');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('switch')->default(0)->comment('是否|开关 0关否1是开');
                $table->decimal('rate', 4, 2)->default(0)->comment('分润比例%');
                $table->string('goods_class_name', 64)->nullable()->comment('商品类名称');
                $table->string('goods_class_code', 64)->nullable()->comment('商品分类逻辑名称');
                $table->unsignedBigInteger('goods_class_picture_uid')->default(0)->comment('分类图片雪花ID(相册图片id)');
                $table->unsignedTinyInteger('is_certificate')->default(0)->comment('是否需要要资质证书 0否1是');
                $table->unsignedTinyInteger('certificate_number')->default(0)->comment('主要资质证书数量');
                $table->string('note', 255)->nullable()->comment('备注说明');
                $table->unsignedTinyInteger('sort')->default(0)->comment('排序');



                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->unique(['goods_class_name','deleted_at'], 'uni_goods_classes_name_del');
                $table->unique(['goods_class_code','deleted_at'], 'uni_goods_classes_code_del');
                $table->index('parent_id', 'idx_goods_classes_parent_id');
                $table->index('deep', 'idx_goods_classes_deep');
                $table->index('is_certificate', 'idx_goods_classes_is_cert');
                $table->index('goods_class_picture_uid', 'idx_goods_classes_pic_uid');
                $table->index('created_time', 'idx_goods_classes_cre_time');
            });

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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }
    }
};
