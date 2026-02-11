<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 19:00:53
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2024-02-25 21:44:10
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'user_infos';
    protected $hasSnowflake = true;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = 'user_uid';
    protected $tableComment = '用户信息表';

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
                    $table->unsignedBigInteger('user_info_uid')->comment('用户信息雪花ID');
                    $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:user_uid%table_count(工具包自动计算)');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->string('nick_name', 64)->default('')->comment('昵称');
                    $table->string('family_name', 32)->default('')->comment('姓');
                    $table->string('name', 64)->default('')->comment('名');
                    $table->string('real_name', 128)->default('')->comment('真实姓名');
                    $table->string('id_number', 32)->nullable()->comment('身份证号');
                    $table->unsignedTinyInteger('sex')->default(0)->comment('性别 0未知10男20女');
                    $table->date('solar_birthday_at')->nullable()->comment('阳历生日');
                    $table->unsignedInteger('solar_birthday_time')->default(0)->comment('阳历生日');
                    $table->date('chinese_birthday_at')->nullable()->comment('阴日生日');
                    $table->unsignedInteger('chinese_birthday_time')->default(0)->comment('阴日生日');
                    $table->string('introduction', 255)->default('')->comment('简介');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_info_uid', 'uni_user_infos_uid_' . $i);
                    $table->unique('id_number', 'uni_user_infos_id_number_' . $i);
                    $table->index('user_uid', 'idx_user_infos_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_infos_created_time_' . $i);
                    $table->index('sex', 'idx_user_infos_sex_' . $i);
                    $table->index('solar_birthday_time', 'idx_user_infos_solar_birthday_' . $i);
                    $table->index('chinese_birthday_time', 'idx_user_infos_chinese_birthday_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');
                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}'");
            }
        }
    }

    /**
     * Reverse migrations.
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
