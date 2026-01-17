<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-05-28 00:08:41
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:59:51
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

        if (!Schema::connection($db_connection)->hasTable('regions')) {
            Schema::connection($db_connection)->create('regions', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedInteger('parent_id')->notNull()->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('深度');
                $table->string('region_name', 64)->notNull()->default('')->comment('地区名称');
                $table->string('region_area', 32)->notNull()->default('')->comment('大区名称');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');



                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->index('parent_id');
                $table->index('deep');
                $table->index('sort');
                $table->index('created_time');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}regions` comment '系统地区表'");
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

        if (Schema::connection($db_connection)->hasTable('regions')) {
            Schema::connection($db_connection)->dropIfExists('regions');
        }
    }
};
