<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-20 17:09:03
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:29:39
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

        if (!Schema::connection($db_connection)->hasTable('user_banks')) {
            Schema::connection($db_connection)->create('user_banks', function (Blueprint $table) {
                $table->id()->comment('主键 用户银行信息表');
                $table->char('user_bank_uid', 20)->comment('用户银行卡雪花ID');
                $table->char('user_uid', 20)->default('')->comment('用户uid');
                $table->unsignedInteger('bank_id')->default(0)->comment('银行id');
                $table->char('bank_front_uid', 20)->default('')->comment('银行卡正面(相册图片雪花ID)');
                $table->char('bank_back_uid', 20)->default('')->comment('银行卡背面(相册图片雪花ID)');
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认 0不 1是');
                $table->string('bank_number', 32)->default('')->comment('银行卡号');
                $table->string('bank_account', 32)->default('')->comment('银行户名');
                $table->string('bank_address', 128)->default('')->comment('开户行地址');
                $table->unsignedTinyInteger('sort')->default(100)->comment('排序');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                // 索引
                $table->unique('user_bank_uid', 'uni_user_banks_uid');
                $table->index('user_uid', 'idx_user_banks_user_uid');
                $table->index('bank_id', 'idx_user_banks_bank_id');
                $table->index('created_time', 'idx_user_banks_created_time');
                $table->index('bank_front_uid', 'idx_user_banks_bank_front_uid');
                $table->index('bank_back_uid', 'idx_user_banks_bank_back_uid');
                $table->index('is_default', 'idx_user_banks_is_default');
                $table->index('sort', 'idx_user_banks_sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_banks` comment '用户银行信息表'");
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

        if (Schema::connection($db_connection)->hasTable('user_banks')) {
            Schema::connection($db_connection)->dropIfExists('user_banks');
        }
    }
};
