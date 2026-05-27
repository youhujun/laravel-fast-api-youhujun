<?php

/*
 * @Descripttion:
 * @version: v1
 * @Author: youhujun 2900976495@qq.com
 * @Date: 2024-05-05 15:06:02
 * @LastEditors: youhujun 2900976495@qq.com
 * @LastEditTime: 2025-01-12 08:45:55
 * @FilePath: \app\Models\LaravelFastApi\V1\Admin\Log\AdminLoginLog.php
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

class AdminLoginLog extends Model
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
        return 'admin_login_logs';
    }

    protected function getShardBusinessKey(): string
    {
        return 'admin_uid';
    }

    protected $fillable = ['admin_login_log_uid','shard_key', 'admin_uid', 'revision','data_type','login_type', 'status', 'instruction', 'ip', 'created_at', 'created_time', 'updated_at', 'updated_time','deleted_at'];
    protected $hidden = ['id', 'revision'];
    protected $table = 'admin_login_logs';
    protected $primaryKey = 'admin_login_log_uid';
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
