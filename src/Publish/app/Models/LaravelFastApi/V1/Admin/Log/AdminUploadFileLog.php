<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-23 00:04:23
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-05-01 23:08:16
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Admin\Log\AdminUploadFileLog.php
 */

namespace App\Models\LaravelFastApi\V1\Admin\Log;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class AdminUploadFileLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithTimeStampFields;
    use WithSnowflakeId;
    use WithCustomConnection;
    use WithShardRouting;

    // 实现抽象方法（替代原来的属性定义）
    public function getBaseTable(): string
    {
        return 'admin_upload_file_logs';
    }

    protected function getShardBusinessKey(): string
    {
        return 'admin_uid';
    }

    protected $fillable = ['admin_upload_file_log_uid','shard_key', 'admin_uid', 'revision', 'save_type', 'use_type', 'file_name', 'file_path', 'file_extension', 'file', 'file_url', 'created_time', 'updated_time'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'admin_upload_file_logs';
    protected $primaryKey = 'admin_upload_file_log_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
