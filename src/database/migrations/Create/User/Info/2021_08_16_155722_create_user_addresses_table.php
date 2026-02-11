<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 15:57:22
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 14:49:00
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_addresses';
    protected $hasSnowflake = true;
		// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户地址表';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                    $table->id()->comment('主键');
                    $table->unsignedBigInteger('user_address_uid')->comment('用户地址雪花ID');
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('address_type')->default(0)->comment('地址类型 默认0家庭10工作20 学校30 其他40');
                    $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认地址 0否1是');
                    $table->unsignedTinyInteger('is_top')->default(0)->comment('是否置顶 0否1是');

                    $table->string('address_info', 255)->default('')->comment('地址详情');
                    $table->string('user_name', 60)->default('')->comment('收货人姓名');
                    $table->char('phone', 12)->default('')->comment('收货人手机号');
                    $table->unsignedTinyInteger('country_id')->default(0)->comment('国家或地区id');
                    $table->unsignedInteger('province_id')->default(0)->comment('省id');
                    $table->unsignedInteger('region_id')->default(0)->comment('地区id');
                    $table->unsignedInteger('city_id')->default(0)->comment('城市id');
                    $table->unsignedBigInteger('towns_id')->default(0)->comment('城镇id');
                    $table->unsignedBigInteger('village_id')->default(0)->comment('小区或村id');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_address_uid', 'uni_user_addresses_uid_' . $i);
                    $table->index('user_uid', 'idx_user_addresses_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_addresses_created_time_' . $i);
                    $table->index('address_type', 'idx_user_addresses_address_type_' . $i);
                    $table->index('is_default', 'idx_user_addresses_is_default_' . $i);
                    $table->index('is_top', 'idx_user_addresses_is_top_' . $i);
                    $table->index('country_id', 'idx_user_addresses_country_id_' . $i);
                    $table->index('towns_id', 'idx_user_addresses_towns_id_' . $i);
                    $table->index('village_id', 'idx_user_addresses_village_id_' . $i);
                    $table->index('province_id', 'idx_user_addresses_province_id_' . $i);
                    $table->index('region_id', 'idx_user_addresses_region_id_' . $i);
                    $table->index('city_id', 'idx_user_addresses_city_id_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}'");
            }
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
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
