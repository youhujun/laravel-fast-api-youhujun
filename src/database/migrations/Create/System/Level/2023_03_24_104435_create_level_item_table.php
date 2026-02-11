<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-24 10:44:35
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 11:21:59
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'level_items';
    protected $hasSnowflake = false;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = '';
    protected $tableComment = '级别配置项表';

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
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');

                $table->unsignedTinyInteger('type')->notNull()->default(0)->comment('配置项类型 10数值 20百分比 30时间');
                $table->string('item_name', 32)->unique()->nullable()->comment('配置项名称 唯一');
                $table->string('item_code', 32)->unique()->nullable()->comment('配置项代码 唯一');
                $table->string('description', 64)->notNull()->default('')->comment('描述');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                // 索引
                $table->unique(['item_name','deleted_at'], 'uni_level_items_name_del');
                $table->unique(['item_code','deleted_at'], 'uni_level_items_code_del');
                $table->index('type', 'idx_level_items_type');
                $table->index('created_time', 'idx_level_items_cre_time');
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
