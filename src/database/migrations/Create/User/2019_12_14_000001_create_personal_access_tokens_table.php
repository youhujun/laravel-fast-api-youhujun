<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-04-01 15:27:02
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-01-16 15:48:39
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'personal_access_tokens';
    protected $hasSnowflake = false;
		// 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
	protected $shardKeyAnchor = '';
    protected $tableComment = '个人token表';

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
                $table->id()->comment('个人token表主键');

                $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');

                $table->string('tokenable_type', 255)->comment('类型');
                $table->bigInteger('tokenable_id')->comment('id');
                $table->string('name')->comment('姓名');
                $table->string('token', 64)->comment('token');
                $table->text('abilities')->nullable()->comment('能力');
                $table->dateTime('last_used_at')->nullable()->comment('最后使用时间');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');

                // 索引
                $table->index('user_uid', 'idx_personal_access_tokens_user_uid');
                $table->index('tokenable_type', 'idx_personal_access_tokens_type');
                $table->index('tokenable_id', 'idx_personal_access_tokens_id');
                $table->index('last_used_at', 'idx_personal_access_tokens_last_used');
                $table->unique('token', 'uni_personal_access_tokens_token');
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
