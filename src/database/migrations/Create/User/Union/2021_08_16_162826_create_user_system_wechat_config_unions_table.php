<?php
/*
 * @Descripttion:
 * @version:
 * @Author: YouHuJun
 * @Date: 2021-08-16 16:28:26
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
    protected $baseTable = 'user_system_wechat_config_unions';
    protected $hasSnowflake = true;
    protected $tableComment = '用户和系统微信配置关联表openid';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $shardConfig = Config::get('youhujun.shard');
        $dbConnection = $shardConfig['default_db'];
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (!Schema::connection($dbConnection)->hasTable($tableName))
            {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {

                    $table->id()->comment('主键');
                    $table->unsignedBigInteger('user_system_wechat_config_union_uid')->comment('用户微信配置关联雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->string('openid',100)->nullable()->comment('唯一openid');
                    $table->string('session_key',100)->default('')->comment('session_key');
                    $table->unsignedTinyInteger('type')->default(30)->comment('类型 10小程序 20小游戏 30公众号 40 测试微信公众号');
                    $table->unsignedInteger('system_wechat_config_id')->default(0)->comment('系统微信配置id');

                    $table->string('access_token',128)->default('')->comment('网页授权接口调用凭证,注意,此access_token与基础支持的access_token不同');
                    $table->unsignedInteger('expires_in')->comment('access_token接口调用凭证超时时间,单位(秒)');

                    $table->string('refresh_token',128)->default('')->comment('用户刷新access_token,有效期30天当refresh_token失效之后，需要用户重新授权。');

                    $table->string('scope',30)->default('')->comment('用户授权的作用域，使用逗号（,）分隔');

                    $table->unsignedTinyInteger('is_snapshotuser')->default(0)->comment('是否为快照页模式虚拟账号，只有当用户是快照页模式虚拟账号时返回，值为1');

                    $table->dateTime('verified_at')->nullable()->comment('微信号认证时间string');
                    $table->unsignedInteger('verified_time')->default(0)->comment('微信号认证时间int');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    // 索引
                    $table->unique('user_system_wechat_config_union_uid', 'uni_sys_wechat_usw_uid_' . $i);
                    $table->unique('openid', 'uni_sys_wechat_openid_' . $i);
                    $table->index('user_uid', 'idx_sys_wechat_user_uid_' . $i);
                    $table->index('created_time', 'idx_sys_wechat_cre_time_' . $i);
                    $table->index('type', 'idx_sys_wechat_type_' . $i);
                    $table->index('system_wechat_config_id', 'idx_sys_wechat_sys_wid_' . $i);
                    $table->index('access_token', 'idx_sys_wechat_acc_token_' . $i);
                    $table->index('refresh_token', 'idx_sys_wechat_ref_token_' . $i);
                    $table->index('updated_time', 'idx_sys_wechat_upd_time_' . $i);
                });

                $prefix = config('database.connections.'.$dbConnection.'.prefix');

                DB::connection($dbConnection)->statement("ALTER TABLE `{$prefix}{$tableName}` comment '{$this->tableComment}-分表{$i}'");
            }
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
        $tableCount = Config::get('youhujun.shard.table_count', 1);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName))
            {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }

    }
};
