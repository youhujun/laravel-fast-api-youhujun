<?php

/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2022-02-09 20:38:38
 * @LastEditors: youhujun youhu8888@163.com
 * @LastEditTime: 2026-02-11 11:50:46
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'password_reset_tokens';
    protected $hasSnowflake = false;
    // 分片键锚定字段 仅做识别用,不参与代码逻辑（格式：*_uid，无分片则为''）
    protected $shardKeyAnchor = '';
    protected $tableComment = '密码重置表';

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
                $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                $table->string('email', 128)->default('')->comment('邮箱');
                $table->char('phone', 12)->default('')->comment('手机号');
                $table->string('token', 255)->default('')->comment('令牌');

                $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');

                $table->index('email', 'idx_pwd_tokens_email');
                $table->index('phone', 'idx_pwd_tokens_phone');
                $table->index('created_time', 'idx_pwd_tokens_cre_time');
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
