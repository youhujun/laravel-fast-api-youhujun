<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2025-07-10 20:41:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-07-10 20:50:02
 * @FilePath: \database\migrations\2025_07_10_204135_create_sessions_table.php
 * Copyright (C) 2025 youhujun. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'sessions';
    protected $hasSnowflake = false;
    protected $tableComment = '用户sessions表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        //注意是否需要修改mysql连接名和表名
        if (!Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table)
            {
                $table->id()->comment('主键');
                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                $table->string('ip_address', 45)->nullable()->comment('ip地址');
                $table->text('user_agent')->nullable()->comment('用户代理');
                $table->longText('payload')->comment('载荷');
                $table->integer('last_activity')->default(0)->comment('最后访问');
                $table->string('note',128)->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->index('user_uid', 'idx_sessions_user_uid');
                $table->index('created_time', 'idx_sessions_cre_time');
            });

            //注意是否需要修改mysql连接名
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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable))
        {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }

    }
};
