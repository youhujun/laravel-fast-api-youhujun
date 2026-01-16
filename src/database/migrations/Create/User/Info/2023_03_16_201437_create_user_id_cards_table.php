<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2023-03-16 20:14:37
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-04-06 16:36:17
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

        if (!Schema::connection($db_connection)->hasTable('user_id_cards')) {
            Schema::connection($db_connection)->create('user_id_cards', function (Blueprint $table) {
                $table->id()->comment('主键');
                $table->char('user_uid', 20)->notNull()->default('')->comment('用户uid');
                $table->unsignedBigInteger('id_card_front_id')->notNull()->default(0)->comment('身份证正面');
                $table->unsignedBigInteger('id_card_back_id')->notNull()->default(0)->comment('身份证背面');
                $table->unsignedBigInteger('revision')->notNull()->default(0)->comment('乐观锁');
                $table->unsignedTinyInteger('sort')->notNull()->default(100)->comment('排序');

                $table->dateTime('created_at')->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->notNull()->default(DB::raw('UNIX_TIMESTAMP()'))->comment('创建时间戳');
                $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->notNull()->default(0)->comment('更新时间戳');
                $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                $table->index('user_uid');
                $table->index('created_time');
                $table->index('id_card_front_id');
                $table->index('id_card_back_id');
                $table->index('sort');
            });

            $prefix = config('database.connections.'.$db_connection.'.prefix');

            DB::connection($db_connection)->statement("ALTER TABLE `{$prefix}user_id_cards` comment '用户身份证表'");
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

        if (Schema::connection($db_connection)->hasTable('user_id_cards')) {
            Schema::connection($db_connection)->dropIfExists('user_id_cards');
        }
    }
};
