<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-07-16 02:54:13
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:38:33
 * @FilePath: \src\database\migrations\Create\System\Config\2024_07_16_025413_create_system_voice_configs_table.php
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

        if (!Schema::connection($db_connection)->hasTable('system_voice_configs')) {
            Schema::connection($db_connection)->create('system_voice_configs', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('admin_uid', 20)->notNull()->default('')->comment('管理员uid,雪花ID');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

                $table->string('voice_title', 32)->notNull()->default('')->comment('提示音标题');
                $table->string('channle_name', 64)->notNull()->default('')->comment('频道名称');
                $table->string('channle_event', 32)->unique()->nullable()->comment('事件名称');
                $table->unsignedTinyInteger('voice_save_type')->notNull()->default(0)->comment('提示音保存方式 10 本地 20存储桶');
                $table->string('voice_url', 128)->notNull()->default('')->comment('提示音url');
                $table->string('voice_path', 128)->notNull()->default('')->comment('提示音路径');
                $table->string('voice_file', 128)->notNull()->default('')->comment('提示音文件名');
                $table->string('note', 128)->notNull()->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->unique(['channle_event','deleted_at']);
                $table->index('voice_save_type');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}system_voice_configs` comment '系统提示音配置表'");
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

        if (Schema::connection($db_connection)->hasTable('system_voice_configs')) {
            Schema::connection($db_connection)->dropIfExists('system_voice_configs');
        }
    }
};
