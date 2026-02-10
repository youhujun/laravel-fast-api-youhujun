<?php
/*
 * @Descripttion: 
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-02-02 15:08:08
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2026-01-23 21:05:57
 * @FilePath: d:\wwwroot\Working\PHP\Components\Laravel\youhujun\laravel-fast-api-base\src\database\migrations\Admin\Log\2023_08_10_104134_create_admin_upload_file_log_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class () extends Migration {
    protected $baseTable = 'admin_upload_file_logs';
    protected $hasSnowflake = true;
    protected $tableComment = '后台文件上传日志表';

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

        //注意是否需要修改mysql连接名和表名
        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (!Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->create($tableName, function (Blueprint $table) use ($i) {
                        $table->unsignedBigInteger('admin_upload_file_log_uid')->default(0)->comment('日志uid,雪花ID');
                        $table->unsignedTinyInteger('shard_key')->default(0)->comment('分片键:admin_uid%table_count(工具包自动计算)');
                        $table->unsignedBigInteger('admin_uid')->default(0)->comment('管理员uid,雪花ID');
                        $table->unsignedBigInteger('revision')->default(0)->comment('乐观锁');
                        $table->unsignedTinyInteger('data_type')->default(1)->comment('冷热数据分离 1热 0冷');

                        $table->unsignedBigInteger('save_type')->default(0)->comment('存储类型 10本地 20存储桶');
                    $table->unsignedBigInteger('use_type')->default(0)->comment('使用类型0  10系统配置 20管理员配置');

                    $table->string('file_name',128)->default('')->comment('文件名');
                    $table->string('file_path',128)->default('')->comment('文件路径');
                    $table->string('file_extension',12)->default('')->comment('文件后缀');
                    $table->string('file',128)->default('')->comment('文件');
                    $table->string('file_url',255)->default('')->comment('文件url (存储桶类型)');

                    // 时间字段（自动填充+索引，关键优化）
                    $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');
                    $table->unsignedInteger('created_time')->default(0)->comment('创建时间戳');
                    $table->dateTime('updated_at')->nullable()->useCurrentOnUpdate()->comment('更新时间');
                    $table->unsignedInteger('updated_time')->default(0)->comment('更新时间戳');
                    $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

                        // 索引
                        $table->unique('admin_upload_file_log_uid', 'uni_admin_upload_file_logs_log_uid_' . $i);
                        $table->index('admin_uid', 'idx_admin_upload_file_logs_admin_uid_' . $i);
                        $table->index('data_type', 'idx_admin_upload_file_logs_data_type_' . $i);

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

        //注意是否需要修改mysql连接名和表名
        for ($i = 0; $i < $tableCount; $i++) {
            $tableName = $this->baseTable . '_' . $i;
            if (Schema::connection($dbConnection)->hasTable($tableName)) {
                Schema::connection($dbConnection)->dropIfExists($tableName);
            }
        }
    }
};
