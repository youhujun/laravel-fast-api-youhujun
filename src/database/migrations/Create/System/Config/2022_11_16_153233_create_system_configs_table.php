<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-11-16 15:32:33
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 18:55:01
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
        if (!Schema::connection($db_connection)->hasTable('system_configs')) {
            Schema::connection($db_connection)->create('system_configs', function (Blueprint $table) {
                $table->increments('id')->comment('主键');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('parent_id')->notNull()->default(0)->comment('父级id');
                $table->unsignedTinyInteger('deep')->notNull()->default(0)->comment('深度级别');
                $table->unsignedTinyInteger('item_type')->notNull()->default(0)->comment('值的类型  10标签 20字符串 30数值 40路径');

                $table->string('item_label', 128)->notNull()->default('')->comment('配置项标签');
                $table->string('item_value', 128)->notNull()->default('')->comment('配置项值');
                $table->decimal('item_price', 32, 8, true)->notNull()->default(0)->comment('配置项数值,一般为金额或积分');
                $table->string('item_path', 255)->notNull()->default('')->comment('文件路径');
                $table->string('item_introduction', 255)->notNull()->default('')->comment('配置项说明');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                // 索引
                $table->index('sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}system_configs` comment '系统配置表'");
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

        if (Schema::connection($db_connection)->hasTable('system_configs')) {
            Schema::connection($db_connection)->dropIfExists('system_configs');
        }
    }
};
