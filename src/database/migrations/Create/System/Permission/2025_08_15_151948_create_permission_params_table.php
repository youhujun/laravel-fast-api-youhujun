<?php

/*
 * @Descripttion: 权限菜单参数表(资源路由用)
 * @version:
 * @Author: YouHuJun
 * @Date: 2025-08-15 15:19:48
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-23 21:20:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'permission_params';
    protected $hasSnowflake = false;
		// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = '';
    protected $tableComment = '权限菜单参数表(资源路由用)';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];

        //注意是否需要修改mysql连接名和表名
        if (!Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table) {
                $table->id()->comment('主键-权限菜单参数表(资源路由用)');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedBigInteger('permission_id')->default(0)->comment('权限菜单表id');
                $table->string('key', 128)->default('')->comment('字段名');
                $table->string('value', 255)->default('')->comment('字段值');
                $table->string('note', 128)->default('')->comment('备注');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                // 索引
                $table->index('permission_id', 'idx_perm_params_perm_id');
                $table->index('created_time', 'idx_perm_params_cre_time');
                $table->index('sort', 'idx_perm_params_sort');
            });

            //注意是否需要修改mysql连接名
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

        if (Schema::connection($dbConnection)->hasTable($this->baseTable)) {
            Schema::connection($dbConnection)->dropIfExists($this->baseTable);
        }
    }
};
