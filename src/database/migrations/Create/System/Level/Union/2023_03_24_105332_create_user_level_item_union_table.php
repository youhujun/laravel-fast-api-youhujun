<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-24 10:53:32
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
    protected $baseTable = 'user_level_item_unions';
    protected $hasSnowflake = false;
    protected $tableComment = '用户级别和配置项关联表';

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
            Schema::connection($dbConnection)->create($this->baseTable, function (Blueprint $table)
            {
                $table->id()->comment('主键');
                $table->unsignedInteger('user_level_id')->notNull()->default(0)->comment('用户级别id');
                $table->unsignedInteger('level_item_id')->notNull()->default(0)->comment('级别配置项id');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('value_type')->notNull()->default(0)->comment('与参数值的关系 10等于 20大于 30小于 40大于等于 50小于等于');
                $table->unsignedInteger('value')->notNull()->default(0)->comment('参数值');

                // 时间字段（自动填充+索引，关键优化）
                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                // 索引
                $table->index('user_level_id', 'idx_user_lvl_itm_uns_user_lvl_id');
                $table->index('level_item_id', 'idx_user_lvl_itm_uns_lvl_itm_id');
                $table->index('value_type', 'idx_user_lvl_itm_uns_val_type');
                $table->index('sort', 'idx_user_lvl_itm_uns_sort');

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
