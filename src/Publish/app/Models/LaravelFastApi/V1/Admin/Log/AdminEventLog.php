<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-07 21:04:51
 * @LastEditors: youhujun youhu8888@163.com & xueer
 * @LastEditTime: 2026-04-26 02:24:33
 * @FilePath: \youhu-laravel-api-12\app\Models\LaravelFastApi\V1\Admin\Log\AdminEventLog.php
 */

namespace App\Models\LaravelFastApi\V1\Admin\Log;

use App\Models\Traits\WithShardRouting;
use App\Models\Traits\WithTimeStampFields;
use App\Models\Traits\WithSnowflakeId;
use App\Models\Traits\WithCustomConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LaravelFastApi\V1\Admin\Admin;
use Illuminate\Database\Eloquent\Builder;

class AdminEventLog extends Model
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
        return 'admin_event_logs';
    }

    protected function getShardBusinessKey(): string
    {
        return 'admin_uid';
    }

    protected $fillable = ['admin_event_log_uid','shard_key', 'admin_uid', 'revision', 'event_type', 'event_route_action','data_type', 'event_name', 'event_code', 'note', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'admin_event_logs';
    protected $primaryKey = 'admin_event_log_uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    // 时间戳格式
    protected $dateFormat = 'Y-m-d H:i:s';
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_uid', 'admin_uid');
    }
}
