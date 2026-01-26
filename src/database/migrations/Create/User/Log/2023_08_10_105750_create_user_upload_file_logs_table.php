<?php
/*
 * @Descripttion: 用户文件上传日志表
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2023-08-16 17:06:35
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2026-01-23 21:20:00
 * @FilePath: d:\wwwroot\Api\Components\Laravel\youhujun\laravel-fast-api\src\database\migrations\User\Log\2023_08_10_105750_create_user_upload_file_log_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    protected $baseTable = 'user_upload_file_logs';
    protected $hasSnowflake = true;
    protected $tableComment = '用户文件上传日志表';

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
                    $table->unsignedBigInteger('user_upload_file_log_uid')->comment('日志uid,雪花ID');
                    $table->unsignedBigInteger('user_uid')->default(0)->comment('用户uid');
                    $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                    $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');

                    $table->unsignedTinyInteger('use_type')->default(0)->comment('使用类型0  10个人配置 20个人文件');
                    $table->unsignedTinyInteger('save_type')->default(0)->comment('存储类型 10本地 20存储桶');

                    $table->string('file_name',128)->default('')->comment('文件名');
                    $table->string('file_path',128)->default('')->comment('文件路径');
                    $table->string('file_extension',12)->default('')->comment('文件后缀');
                    $table->string('file',128)->default('')->comment('文件');
                    $table->string('file_url',255)->default('')->comment('文件url(存储桶类型)');

                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间');

                    $table->unique('user_upload_file_log_uid', 'uni_user_upload_file_logs_uid_' . $i);
                    $table->index('user_uid', 'idx_user_upload_file_logs_user_uid_' . $i);
                    $table->index('created_time', 'idx_user_upload_file_logs_created_' . $i);
                    $table->index('data_type', 'idx_user_upload_file_logs_data_type_' . $i);
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
