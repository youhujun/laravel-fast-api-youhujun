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

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $db_connection = config('youhujun.db_connection');

        if (!Schema::connection($db_connection)->hasTable('user_addresses')) {
            Schema::connection($db_connection)->create('user_addresses', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('address_type')->notNull()->default(0)->comment('地址类型 默认0家庭10工作20 学校30 其他40');
                $table->unsignedTinyInteger('is_default')->notNull()->default(0)->comment('是否默认地址 0否1是');
                $table->unsignedTinyInteger('is_top')->notNull()->default(0)->comment('是否置顶 0否1是');

                $table->string('address_info', 255)->notNull()->default('')->comment('地址详情');
                $table->string('user_name', 60)->notNull()->default('')->comment('收货人姓名');
                $table->char('phone', 12)->notNull()->default('')->comment('收货人手机号');
                $table->unsignedTinyInteger('country_id')->notNull()->default(0)->comment('国家或地区id');
                $table->unsignedInteger('province_id')->notNull()->default(0)->comment('省id');
                $table->unsignedInteger('region_id')->notNull()->default(0)->comment('地区id');
                $table->unsignedInteger('city_id')->notNull()->default(0)->comment('城市id');
                $table->unsignedBigInteger('towns_id')->notNull()->default(0)->comment('城镇id');
                $table->unsignedBigInteger('village_id')->notNull()->default(0)->comment('小区或村id');

                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->index('user_uid');
                $table->index('created_time');
                $table->index('address_type');
                $table->index('is_default');
                $table->index('is_top');
                $table->index('province_id');
                $table->index('region_id');
                $table->index('city_id');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_addresses` comment '用户地址表'");
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

        if (Schema::connection($db_connection)->hasTable('user_addresses')) {
            Schema::connection($db_connection)->dropIfExists('user_addresses');
        }
    }
};
