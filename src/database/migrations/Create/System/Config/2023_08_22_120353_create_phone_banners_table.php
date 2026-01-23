<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-22 12:03:53
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-17 11:30:04
 * @FilePath: \src\database\migrations\Create\System\Config\2023_08_22_120353_create_phone_banners_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'phone_banners';
    protected $hasSnowflake = false;
    protected $tableComment = '手机轮播图';

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
                $table->id()->comment('主键-手机轮播图');
                $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                $table->unsignedBigInteger('album_picture_uid')->default(0)->comment('相册图片uid,雪花ID');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->string('redirect_url', 128)->nullable()->comment('跳转路径');
                $table->string('note', 128)->nullable()->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');


                // 索引
                $table->index('album_picture_uid', 'idx_phone_banners_pic_uid');
                $table->index('sort', 'idx_phone_banners_sort');
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
